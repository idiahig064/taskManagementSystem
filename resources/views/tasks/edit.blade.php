<x-app-layout>
    <div class="max-w-3xl mx-auto mt-6 bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg text-gray-700 dark:text-gray-300">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Edit Task</h2>
        <form action="{{ route('tasks.update', $task) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Title --}}
            <div>
                <label for="title" class="block mb-2 text-sm font-semibold">Title</label>
                <input type="text" name="title" id="title" value="{{ $task->title }}"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2 focus:ring focus:ring-blue-400">
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block mb-2 text-sm font-semibold">Description</label>
                <textarea name="description" id="description" rows="4"
                          class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2 focus:ring focus:ring-blue-400">{{ $task->description }}</textarea>
            </div>

            {{-- Priority --}}
            <div>
                <label for="priority" class="block mb-2 text-sm font-semibold">Priority</label>
                <select name="priority" id="priority"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2 focus:ring focus:ring-blue-400">
                    <option value="">Select Priority</option>
                    <option value="Low" class="text-green-500" {{ $task->priority == 'Low' ? 'selected' : '' }}>Low</option>
                    <option value="Medium" class="text-blue-600" {{ $task->priority == 'Medium' ? 'selected' : '' }}>Medium</option>
                    <option value="High" class="text-orange-500" {{ $task->priority == 'High' ? 'selected' : '' }}>High</option>
                    <option value="Urgent" class="text-red-700 font-bold" {{ $task->priority == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>

            {{-- Category --}}
            <div>
                <label for="category_id" class="block mb-2 text-sm font-semibold">Category</label>
                <div class="flex items-center gap-3">
                    <div id="color_category" class="w-6 h-6 rounded-full border border-gray-400"></div>
                    <select name="category_id" id="category_id"
                            class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2 focus:ring focus:ring-blue-400">
                        <option value="0" data-color="#ffffff">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" data-color="{{ $category->color_code }}" {{ $task->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Due Date --}}
            <div>
                <label for="due_date" class="block mb-2 text-sm font-semibold">Due Date</label>
                <input type="date" name="due_date" id="due_date" value="{{ $task->due_date }}"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2 focus:ring focus:ring-blue-400">
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="block mb-2 text-sm font-semibold">Status</label>
                <select name="status" id="status"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2 focus:ring focus:ring-blue-400">
                    <option value="Incomplete" {{ $task->status == 'Incomplete' ? 'selected' : '' }}>Incomplete</option>
                    <option value="Completed" {{ $task->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            {{-- File Attachments --}}
            <div>
                <label for="file_attachments" class="block mb-2 text-sm font-semibold">Attach Files</label>
                <input type="file" name="file_attachments[]" id="file_attachments" multiple
                       class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2 focus:ring focus:ring-blue-400">

                <div class="mt-3">
                    <p class="text-sm font-medium mb-1">Current Attachments:</p>
                    @forelse($attachments as $attachment)
                        <div class="flex items-center justify-between bg-gray-100 dark:bg-gray-800 px-3 py-2 rounded-md mb-2">
                            <div class="flex items-center">
                                <a href="{{ asset($attachment->file_path) }}" target="_blank" class="text-blue-600 hover:underline">
                                    {{ $attachment->file_name }}
                                </a>
                                <span class="text-xs text-gray-500 ml-2">
                                    ({{ number_format($attachment->file_size / 1024, 1) }} KB)
                                </span>
                            </div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="delete_attachments[]" value="{{ $attachment->id }}"
                                       class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Delete</span>
                            </label>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm italic">No attachments</p>
                    @endforelse
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg transition-all">
                    Update Task
                </button>
            </div>
        </form>
    </div>

    {{-- Scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categorySelect = document.querySelector('#category_id');
            const colorCategory = document.querySelector('#color_category');
            const prioritySelect = document.querySelector('#priority');

            function updateCategoryColor() {
                const selected = categorySelect.options[categorySelect.selectedIndex];
                colorCategory.style.backgroundColor = selected.getAttribute('data-color');
            }

            function updatePriorityTextColor() {
                const selected = prioritySelect.options[prioritySelect.selectedIndex];
                prioritySelect.className = selected.className + ' w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2';
            }

            categorySelect.addEventListener('change', updateCategoryColor);
            prioritySelect.addEventListener('change', updatePriorityTextColor);

            updateCategoryColor();
            updatePriorityTextColor();
        });
    </script>
</x-app-layout>
