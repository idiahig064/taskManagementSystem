<x-app-layout>
    <div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <form action="/tasks" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" value="{{ old('title') }}">
                @error('title')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                <textarea name="description" id="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">{{ old('description') }}</textarea>
                @error('description')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Due date</label>
                <input type="date" name="due_date" id="due_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" value="{{ old('due_date') }}">
                @error('due_date')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="priority" class="block text-gray-400 text-sm font-bold mb-2">Priority</label>
                <select name="priority" id="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                    <option value="">Select Priority</option>
                    <option value="Low" class="text-green-400" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                    <option value="Medium" class="text-blue-600" {{ old('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                    <option value="High" class="text-orange-500" {{ old('priority') == 'High' ? 'selected' : '' }}>High</option>
                    <option value="Urgent" class="text-red-700 font-bold" {{ old('priority') == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
                @error('priority')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="Category" class="block text-gray-400 text-sm font-bold mb-2">Category</label>
                <div class="flex gap-3">
                    <div id="color_category" class="w-6 h-6 mt-2 border border-gray-300"></div>
                    <select name="category_id" id="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                        <option value="0" data-color="#ffffff">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" data-color="{{ $category->color_code }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('category_id')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="file_attachment" class="block text-sm font-medium text-gray-700 dark:text-gray-300">File Attachment</label>
                <input type="file" name="file_attachments[]" id="file_attachments" multiple
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                @error('file_attachment')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Create</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categorySelect = document.querySelector('#category_id');
            const colorCategory = document.querySelector('#color_category');
            const setColor = () => {
                const selectedOption = categorySelect.options[categorySelect.selectedIndex];
                colorCategory.style.backgroundColor = selectedOption.getAttribute('data-color');
            };
            categorySelect.addEventListener('change', setColor);
            setColor(); // Set initial color

            const prioritySelect = document.querySelector('#priority');
            const setPriorityColor = () => {
                const selectedOption = prioritySelect.options[prioritySelect.selectedIndex];
                prioritySelect.style.color = selectedOption.className;
            };
            prioritySelect.addEventListener('change', setPriorityColor);
            setPriorityColor(); // Set initial color
        });
    </script>
</x-app-layout>
