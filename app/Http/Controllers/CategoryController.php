<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of all categories with their children.
     */
    public function index()
    {
        $categories = Category::with('children')->orderBy('id')->get();
        $noCategories = $categories->isEmpty();

        return view('categories.index', compact('categories', 'noCategories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $allCategories = Category::all();
        return view('categories.create', compact('allCategories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateCategory($request);

        // Set null if parent is 0 (no parent)
        if ((int)$data['parent_id'] === 0) {
            $data['parent_id'] = null;
        }

        // Assign next available position
        $data['position'] = Category::max('position') + 1;

        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category (for API use).
     */
    public function show(Category $category)
    {
        return $category;
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        $allCategories = Category::where('id', '!=', $category->id)->get();
        return view('categories.edit', compact('category', 'allCategories'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $data = $this->validateCategory($request);

        // Prevent assigning itself as parent
        if ((int)$data['parent_id'] === $category->id) {
            return back()->withErrors(['parent_id' => 'A category cannot be its own parent.']);
        }

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Soft delete a category along with its tasks and optionally its child categories.
     */
    public function destroy(Category $category)
    {
        $this->deleteTasks($category);

        if ($category->isParent()) {
            foreach ($category->children as $child) {
                $this->deleteTasks($child);
                $child->delete();
            }
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category and related tasks moved to trash.');
    }

    /**
     * Reorder categories by updating their position.
     */
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*.id' => ['required', 'integer'],
            'order.*.position' => ['required', 'integer'],
        ]);

        foreach ($data['order'] as $item) {
            Category::where('id', $item['id'])->update(['position' => $item['position']]);
        }

        return response()->json(['message' => 'Category order updated successfully.']);
    }

    /**
     * Validate incoming request for creating or updating a category.
     */
    protected function validateCategory(Request $request): array
    {
        return $request->validate([
            'name' => ['required'],
            'color_code' => ['required', 'unique:categories,color_code,' . ($request->route('category')?->id)],
            'description' => ['required'],
            'parent_id' => ['required', 'integer'],
        ]);
    }

    /**
     * Delete all tasks associated with a category.
     */
    protected function deleteTasks(Category $category): void
    {
        $category->tasks()->each(function ($task) {
            $task->delete();
        });
    }

    // Optional: recursive delete for full cleanup (attachments, comments, etc.)
    /*
    protected function deleteCategoryAndRelations(Category $category)
    {
        $category->tasks()->each(function($task) {
            foreach ($task->attachments as $attachment) {
                if (file_exists(public_path($attachment->file_path))) {
                    unlink(public_path($attachment->file_path));
                }
            }

            $task->attachments()->delete();
            $task->comments()->delete();
            $task->history()->delete();
            $task->delete();
        });

        foreach ($category->children as $child) {
            $this->deleteCategoryAndRelations($child);
        }

        $category->delete();
    }
    */
}
