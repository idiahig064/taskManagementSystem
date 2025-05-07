<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Task;
use App\Models\TaskHistory;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks with optional filters and sorting.
     */
    public function index(Request $request)
    {
        $sortable = ['id', 'created_at', 'due_date', 'priority'];
        $sortBy = in_array($request->query('sort_by'), $sortable) ? $request->query('sort_by') : 'id';
        $sortOrder = $request->query('sort_order', 'asc');

        $query = Task::with(['category', 'user'])
            ->whereHas('category', fn($q) => $q->whereNull('deleted_at'));

        // Apply filters
        $this->applyFilters($query, $request);

        $tasks = $query->orderBy($sortBy, $sortOrder)->paginate(10);
        $categories = Category::whereNull('deleted_at')->orderBy('name')->get();

        return view('tasks.index', compact('tasks', 'sortBy', 'sortOrder', 'categories'))->with('noTasks', $tasks->isEmpty());
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        return view('tasks.create', ['categories' => Category::all()]);
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate(Task::rulesCreate());
        $task = Task::create(array_merge($data, ['user_id' => auth()->id()]));

        $this->handleFileUploads($request, $task);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully');
    }

    /**
     * Display the specified task.
     */
    public function show(int $id)
    {
        $task = Task::with(['comments', 'history' => function($query) {
            $query->latest();
        }])->findOrFail($id);

        $categoryInfo = $this->findCategory($task->category_id);
        $attachments = $this->getAttachments($task->id);

        // Process history logs
        $processedHistory = $task->history->map(function ($log) {
            return $this->prepareHistoryLog($log);
        });

        return view('tasks.show', compact('task', 'attachments', 'processedHistory', 'categoryInfo'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        $attachments = $this->getAttachments($task->id);

        return view('tasks.edit', [
            'task' => $task,
            'categories' => Category::all(),
            'attachments' => $attachments
        ]);
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task)
    {
        $data = $request->validate(Task::rulesUpdate());

        $this->logTaskChanges($task, $data);

        $this->handleFileDeletions($request, $task);
        $this->handleFileUploads($request, $task);

        return redirect()->route('tasks.show', $task)->with('success', 'Task updated successfully');
    }

    /**
     * Soft delete the specified task.
     */
    public function destroy(Task $task)
    {
        TaskHistory::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'old_value' => json_encode($task->toArray()),
        ]);

        $task->delete();
        $task->attachments()->delete();
        $task->comments()->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully');
    }

    /**
     * Store a new comment for a task.
     */
    public function storeComment(Request $request, Task $task)
    {
        $data = $request->validate(['comment' => 'required|string']);

        $task->comments()->create(array_merge($data, [
            'user_id' => auth()->id(),
        ]));

        return redirect()->route('tasks.show', $task);
    }

    /**
     * Retrieve the name of a user by ID.
     */
    public static function nameUserComment(int $id)
    {
        return User::findOrFail($id)->name;
    }

    /**
     * Retrieve a category by ID.
     */
    public static function findCategory(int $id)
    {
        return Category::findOrFail($id);
    }

    /**
     * Apply filtering logic to the task query.
     */
    protected function applyFilters($query, Request $request)
    {
        foreach (['status', 'category_id', 'priority'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->$field);
            }
        }

        if ($request->filled('due_date')) {
            $query->whereDate('due_date', $request->due_date);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
    }

    /**
     * Retrieve file attachments for a task.
     */
    protected function getAttachments(int $taskId)
    {
        return \DB::table('task_attachments')->where('task_id', $taskId)->get();
    }

    /**
     * Prepare history log data for display.
     */
    protected function prepareHistoryLog($log)
    {
        $user = User::find($log->user_id)?->name ?? 'Unknown';
        $field = ucfirst(str_replace('_', ' ', $log->action));
        $old = $log->old_value;
        $new = $log->new_value;
        $isFileAction = in_array($log->action, ['files_added', 'files_removed']);

        if ($log->action === 'category_id') {
            $old = Category::find($old)?->name ?? '—';
            $new = Category::find($new)?->name ?? '—';
            $field = 'Category';
        } elseif ($isFileAction) {
            $field = ($log->action === 'files_added') ? 'Files Added' : 'Files Removed';
            $old = $old ? json_decode($old) : [];
            $new = $new ? json_decode($new) : [];
        }

        return [
            'user' => $user,
            'field' => $field,
            'old' => $old,
            'new' => $new,
            'isFileAction' => $isFileAction,
            'date' => $log->created_at->format('d M Y, H:i')
        ];
    }

    /**
     * Handle file uploads for a task.
     */
    protected function handleFileUploads(Request $request, Task $task)
    {
        if ($request->hasFile('file_attachments')) {
            $addedFiles = [];

            foreach ($request->file('file_attachments') as $file) {
                $uniqueFilename = time() . '_' . $file->getClientOriginalName();
                $path = 'attachments/' . $uniqueFilename;

                \DB::table('task_attachments')->insert([
                    'task_id' => $task->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $file->move(public_path('attachments'), $uniqueFilename);
                $addedFiles[] = $file->getClientOriginalName();
            }

            if (!empty($addedFiles)) {
                TaskHistory::create([
                    'task_id' => $task->id,
                    'user_id' => auth()->id(),
                    'action' => 'files_added',
                    'new_value' => json_encode($addedFiles),
                ]);
            }
        }
    }

    /**
     * Handle deletion of selected attachments.
     */
    protected function handleFileDeletions(Request $request, Task $task)
    {
        if ($request->has('delete_attachments')) {
            $removedFiles = [];

            foreach ($request->delete_attachments as $attachmentId) {
                $attachment = \DB::table('task_attachments')
                    ->where('id', $attachmentId)
                    ->where('task_id', $task->id)
                    ->first();

                if ($attachment) {
                    if (file_exists(public_path($attachment->file_path))) {
                        unlink(public_path($attachment->file_path));
                    }
                    \DB::table('task_attachments')->where('id', $attachmentId)->delete();
                    $removedFiles[] = $attachment->file_name;
                }
            }

            if (!empty($removedFiles)) {
                TaskHistory::create([
                    'task_id' => $task->id,
                    'user_id' => auth()->id(),
                    'action' => 'files_removed',
                    'old_value' => json_encode($removedFiles),
                ]);
            }
        }
    }

    /**
     * Log changes made to the task fields.
     */
    protected function logTaskChanges(Task $task, array $data)
    {
        $oldData = $task->only(['title', 'description', 'due_date', 'priority', 'category_id', 'status']);
        $task->update($data);
        $newData = $task->only(['title', 'description', 'due_date', 'priority', 'category_id', 'status']);

        foreach ($oldData as $key => $oldValue) {
            if ($oldValue != $newData[$key]) {
                TaskHistory::create([
                    'task_id' => $task->id,
                    'user_id' => auth()->id(),
                    'action' => $key,
                    'old_value' => $oldValue,
                    'new_value' => $newData[$key],
                ]);
            }
        }
    }
}
