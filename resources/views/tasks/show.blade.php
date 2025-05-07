<x-app-layout>
    <div class="max-w-5xl mx-auto mt-6 bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg text-gray-700 dark:text-gray-300">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Task Details</h2>

        <!-- Task Details Table -->
        <div class="overflow-x-auto mb-8">
            <table class="w-full text-sm text-left rtl:text-right">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-3">Id</th>
                    <th class="px-6 py-3">Title</th>
                    <th class="px-6 py-3">Category</th>
                    <th class="px-6 py-3">Created</th>
                    <th class="px-6 py-3">Due</th>
                    <th class="px-6 py-3">Priority</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <td class="px-6 py-4">{{ $task->id }}</td>
                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $task->title }}</td>
                    <td class="px-6 py-4" style="background-color: {{ $categoryInfo->color_code }}">
                        {{ $categoryInfo->name }}
                    </td>
                    <td class="px-6 py-4">{{ $task->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}</td>
                    @switch($task->priority)
                        @case('Low')
                            <td class="px-6 py-4 text-green-500 font-semibold">{{ $task->priority }}</td>
                            @break
                        @case('Medium')
                            <td class="px-6 py-4 text-blue-500 font-semibold">{{ $task->priority }}</td>
                            @break
                        @case('High')
                            <td class="px-6 py-4 text-orange-500 font-semibold">{{ $task->priority }}</td>
                            @break
                        @case('Urgent')
                            <td class="px-6 py-4 text-red-700 font-bold">{{ $task->priority }}</td>
                            @break
                    @endswitch
                    @switch($task->status)
                        @case('Completed')
                            <td class="px-6 py-4 text-green-600 font-semibold">{{ $task->status }}</td>
                            @break
                        @case('Incomplete')
                            <td class="px-6 py-4 text-red-600 font-semibold">{{ $task->status }}</td>
                            @break
                    @endswitch
                    <td class="px-6 py-4 space-x-2">
                        <a href="{{ route('tasks.edit', $task) }}" class="inline-block bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-md text-sm">Edit</a>
                        <button type="button" onclick="openModal({{ $task->id }})"
                                class="inline-block bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md text-sm">Delete</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- File Attachments Section -->
        <div class="mb-8 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">File Attachments</h3>

            @if($attachments->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($attachments as $attachment)
                        <div class="flex items-center justify-between bg-white dark:bg-gray-700 p-3 rounded-md border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center space-x-3">
                                @php
                                    $extension = pathinfo($attachment->file_name, PATHINFO_EXTENSION);
                                    $icon = match(strtolower($extension)) {
                                        'pdf' => 'file-pdf',
                                        'doc', 'docx' => 'file-word',
                                        'xls', 'xlsx' => 'file-excel',
                                        'jpg', 'jpeg', 'png', 'gif' => 'file-image',
                                        default => 'file'
                                    };
                                @endphp
                                <i class="fas fa-{{ $icon }} text-gray-500 dark:text-gray-300 text-xl"></i>
                                <div>
                                    <a href="{{ asset($attachment->file_path) }}" target="_blank"
                                       class="text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ \Illuminate\Support\Str::limit($attachment->file_name, 25) }}
                                    </a>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ number_format($attachment->file_size / 1024, 1) }} KB
                                    </p>
                                </div>
                            </div>
                            <a href="{{ asset($attachment->file_path) }}" download
                               class="text-gray-500 hover:text-blue-600 dark:hover:text-blue-400">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 italic">No attachments found</p>
            @endif
        </div>

        <!-- Description Section -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Description</h3>
            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                <p class="text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $task->description ?? 'No description provided' }}</p>
            </div>
        </div>

        <!-- Task History Section -->
        <div class="mb-10">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Task History</h3>

            @if($processedHistory->count())
                <div class="space-y-6 border-l-2 border-gray-300 dark:border-gray-600 pl-4">
                    @foreach($processedHistory as $log)
                        <div class="relative pl-6">
                            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $log['date'] }}</span>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $log['user'] }}</span>
                                </div>

                                @if($log['isFileAction'])
                                    <div class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                        <span class="font-semibold">{{ $log['field'] }}</span>
                                    </div>
                                    <div class="space-y-1">
                                        @if($log['field'] === 'Files Added')
                                            @foreach($log['new'] as $filename)
                                                <div class="flex items-center text-sm">
                                                    <span class="text-green-600">+</span>
                                                    <span class="ml-2 text-green-600">{{ $filename }}</span>
                                                </div>
                                            @endforeach
                                        @else
                                            @foreach($log['old'] as $filename)
                                                <div class="flex items-center text-sm">
                                                    <span class="text-red-500">-</span>
                                                    <span class="ml-2 text-red-500">{{ $filename }}</span>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                @else
                                    <div class="text-sm text-gray-700 dark:text-gray-300">
                                        <span class="font-semibold">{{ $log['field'] }}</span> changed:
                                    </div>
                                    <div class="mt-1 text-sm">
                                        <span class="text-red-500">{{ $log['old'] ?? '—' }}</span>
                                        <span class="mx-2 text-gray-500">→</span>
                                        <span class="text-green-600 font-semibold">{{ $log['new'] ?? '—' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 text-gray-500 dark:text-gray-400 italic">No history records found.</div>
            @endif
        </div>


        <!-- Comments Section -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Comments</h3>

            <form action="{{ route('tasks.comments.store', $task) }}" method="POST" class="space-y-4 mt-4">
                @csrf
                <textarea name="comment" rows="3" placeholder="Add a comment..."
                          class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2 focus:ring focus:ring-blue-400"
                          required></textarea>
                <button type="submit"
                        class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2.5 rounded-lg text-sm font-medium">
                    Add Comment
                </button>
            </form>

            <div class="mt-6 space-y-4">
                @forelse ($task->comments as $comment)
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-start">
                            <div>
                                <strong class="text-gray-900 dark:text-white">{{ (new \App\Http\Controllers\TaskController)->nameUserComment($comment->user_id) }}</strong>
                                <p class="text-gray-600 dark:text-gray-300 mt-1">{{ $comment->comment }}</p>
                            </div>
                            <small class="text-gray-400">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400 italic">No comments yet</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75 z-50 hidden">
        <div class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow-xl max-w-md w-full">
            <h2 class="text-lg font-bold mb-4 text-gray-800 dark:text-white">Confirm Deletion</h2>
            <p class="text-gray-600 dark:text-gray-300">Are you sure you want to delete this task? This action cannot be undone.</p>
            <div class="mt-6 flex justify-end space-x-3">
                <button onclick="closeModal()"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Cancel</button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Delete</button>
                </form>
            </div>
        </div>
    </div>


    <script>
        function openModal(taskId) {
            document.getElementById('deleteForm').action = `/tasks/${taskId}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
