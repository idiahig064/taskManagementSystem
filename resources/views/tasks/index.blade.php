<x-app-layout>
    <div class="relative overflow-x-auto">
        <a href="{{ route('tasks.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mt-4 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Create</a>

        <!-- Filters -->
        <form method="GET" action="{{ route('tasks.index') }}" class="my-4">
            <div class="flex gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tasks" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                    <option value="">All Statuses</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Incomplete" {{ request('status') == 'Incomplete' ? 'selected' : '' }}>Incomplete</option>
                </select>
                <select name="category_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="priority" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                    <option value="">All Priorities</option>
                    <option value="Low" {{ request('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                    <option value="Medium" {{ request('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                    <option value="High" {{ request('priority') == 'High' ? 'selected' : '' }}>High</option>
                    <option value="Urgent" {{ request('priority') == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
                <input type="date" name="due_date" value="{{ request('due_date') }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-md shadow-sm hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Filter</button>
            </div>
        </form>

        @if($noTasks)
            <p class="text-center text-gray-500 dark:text-gray-400 mt-4">No tasks found.</p>
        @else
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 mt-4">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Id</th>
                    <th scope="col" class="px-6 py-3">Title</th>
                    <th scope="col" class="px-6 py-3">Category</th>
                    <th scope="col" class="px-6 py-3">
                        <a href="{{ route('tasks.index', ['sort_by' => 'created_at', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc']) }}">
                            Creation Date @if(request('sort_by') == 'created_at') {{ request('sort_order') == 'asc' ? '▼' : '▲' }} @endif
                        </a>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <a href="{{ route('tasks.index', ['sort_by' => 'due_date', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc']) }}">
                            Due Date @if(request('sort_by') == 'due_date') {{ request('sort_order') == 'asc' ? '▼' : '▲' }} @endif
                        </a>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <a href="{{ route('tasks.index', ['sort_by' => 'priority', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc']) }}">
                            Priority @if(request('sort_by') == 'priority') {{ request('sort_order') == 'asc' ? '▼' : '▲' }} @endif
                        </a>
                    </th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Actions</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($tasks as $task)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-400 hover:dark:bg-gray-900">
                        <td class="px-6 py-4">{{ $task->id }}</td>
                        <td class="px-6 py-4 font-bold text-black dark:text-white"><a href="{{ route('tasks.show', $task) }}">{{ $task->title }}</a></td>
{{--                        <td class="px-6 py-4 text-gray-900" style="background-color: {{ (new \App\Http\Controllers\TaskController)->findCategory($task->category_id)['color_code'] }}">{{ (new \App\Http\Controllers\TaskController)->findCategory($task->category_id)['name'] }}</td>--}}
                        <td class="px-6 py-4 text-gray-900" style="background-color: {{ $task->category->color_code }}">
                            {{ $task->category->name }}
                        </td>
                        {{--                        <td class="px-6 py-4 text-gray-900" style="background-color: {{ $task->category->color_code ?? '#cccccc' }}">--}}
{{--                            {{ $task->category->name ?? 'Deleted Category' }}--}}
{{--                        </td>--}}
                        <td class="px-6 py-4">{{ $task->created_at }}</td>
                        <td class="px-6 py-4">{{ $task->due_date }}</td>
                        @switch($task->priority)
                            @case('Low')
                                <td class="px-6 text-green-400">{{ $task->priority }}</td>
                                @break
                            @case('Medium')
                                <td class="px-6 text-blue-600">{{ $task->priority }}</td>
                                @break
                            @case('High')
                                <td class="px-6 text-orange-500">{{ $task->priority }}</td>
                                @break
                            @case('Urgent')
                                <td class="px-6 text-red-700 font-bold">{{ $task->priority }}</td>
                                @break
                        @endswitch
                        @switch($task->status)
                            @case('Completed')
                                <td class="pe-6 text-green-600">{{ $task->status }}</td>
                                @break
                            @case('Incomplete')
                                <td class="pe-6 text-red-600">{{ $task->status }}</td>
                                @break
                        @endswitch
                        <td class="px-6 py-4 space-x-2">
                            <a href="{{ route('tasks.edit', $task) }}" class="inline-block bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-md text-sm">Edit</a>
                            <button type="button" onclick="openModal({{ $task->id }})"
                                    class="inline-block bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md text-sm">Delete</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="mt-4">{{ $tasks->links() }}</div>
    </div>
    @endif

    <!-- Modal -->
    <div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75 hidden">
        <div class="bg-white rounded-lg p-6">
            <h2 class="text-lg font-bold mb-4">Confirm Deletion</h2>
            <p>Are you sure you want to delete this task?</p>
            <div class="mt-4 flex justify-end">
                <button onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Delete</button>
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
