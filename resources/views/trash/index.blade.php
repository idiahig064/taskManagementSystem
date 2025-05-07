<!-- resources/views/trash/index.blade.php -->
<x-app-layout>
    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Trash Management</h2>
                <div class="flex space-x-4">
                    <a href="{{ route('categories.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Back to Categories
                    </a>
                </div>
            </div>

            @if($deletedCategories->isEmpty())
                <div class="text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No items in trash</h3>
                    <p class="mt-1 text-sm text-gray-500">Deleted categories and tasks will appear here.</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($deletedCategories as $category)
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                            <!-- Category Header with Color Indicator -->
                            <div class="px-6 py-4 border-b" style="background-color: {{ $category->color_code }}; color: white;">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 rounded-full border-2 border-white mr-3"
                                             style="background-color: {{ $category->color_code }}"></div>
                                        <h3 class="text-lg font-semibold flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A1 1 0 019.293 9L12 11.586 14.707 9a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $category->name }}
                                        </h3>
                                    </div>
                                    <span class="text-sm bg-white/20 px-2 py-1 rounded">
                                        Deleted {{ $category->deleted_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            <div class="px-6 py-4">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Restore Options</h4>
                                    <div class="flex space-x-3">
                                        <form action="{{ route('trash.restore', $category->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" name="restore_category"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-black bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                                Restore Category Only
                                            </button>
                                        </form>

                                        @if($category->tasks->count())
                                            <form action="{{ route('trash.restore', $category->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" name="restore_tasks"
                                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-black bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition">
                                                    Restore With All Tasks
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                @if($category->tasks->count())
                                    <div class="mt-4">
                                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Select Tasks to Restore</h4>
                                        <form action="{{ route('trash.restore', $category->id) }}" method="POST" id="taskForm-{{ $category->id }}">
                                            @csrf
                                            <div class="space-y-2">
                                                @foreach($category->tasks as $task)
                                                    <div class="flex justify-between items-center bg-gray-50 rounded-lg px-4 py-3 hover:bg-gray-100 transition gap-3">
                                                        <div class="flex items-center space-x-3">
                                                            <input type="checkbox" name="selected_tasks[]"
                                                                   value="{{ $task->id }}"
                                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded task-checkbox"
                                                                   form="taskForm-{{ $category->id }}">
                                                            <span class="text-sm font-medium text-gray-700">{{ $task->title }}</span>
                                                        </div>
                                                        <span class="text-xs text-gray-500">
                                                            {{ $task->deleted_at->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="pt-4">
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-black bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition disabled:opacity-50"
                                                        id="restoreSelectedBtn-{{ $category->id }}"
                                                        disabled>
                                                    Restore Selected Tasks and Category
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enable/disable restore selected button based on checkbox selection
            document.querySelectorAll('.task-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const formId = this.getAttribute('form');
                    const form = document.getElementById(formId);
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const checkedBoxes = form.querySelectorAll('input[type="checkbox"]:checked');

                    submitBtn.disabled = checkedBoxes.length === 0;
                });
            });
        });
    </script>
</x-app-layout>
