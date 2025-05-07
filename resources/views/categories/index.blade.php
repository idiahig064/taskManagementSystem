<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-white leading-tight">
            Categories
        </h2>
    </x-slot>

    <!-- Add category button -->
    <a href="{{ route('categories.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mt-4 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Create Category</a>

    <div class="py-10 max-w-5xl mx-auto px-4">
        @if ($noCategories)
            <div class="text-center text-gray-500 text-lg">
                No categories available.
            </div>
        @else
            <ul id="sortable" class="space-y-6">
                @foreach ($categories->where('parent_id', 0)->sortBy('position') as $parent)
                    <li data-id="{{ $parent->id }}">
                        <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow">
                            <!-- Parent Header -->
                            <div class="flex justify-between items-center px-5 py-3 text-white text-lg font-semibold" style="background-color: {{ $parent->color_code }}">
                                <span>{{ $parent->name }}</span>
                                <div class="space-x-2">
                                    <a href="{{ route('categories.edit', $parent) }}" class="bg-white/20 hover:bg-white/30 px-3 py-1 text-sm rounded">
                                        Edit
                                    </a>
                                    <button onclick="openModal('deleteModal-{{ $parent->id }}')" class="bg-white/20 hover:bg-white/30 px-3 py-1 text-sm rounded text-red-200 hover:text-red-100">
                                        Delete
                                    </button>
                                </div>
                            </div>

                            <!-- Parent Content -->
                            <div class="px-5 py-4">
                                <p class="text-gray-700 mb-2">{{ $parent->description }}</p>

                                @if ($parent->children->isNotEmpty())
                                    <div class="mt-3">
                                        <p class="text-sm font-medium text-gray-500 mb-2">Subcategories:</p>
                                        <ul class="space-y-2">
                                            @foreach ($parent->children as $child)
                                                <li class="flex justify-between items-center bg-gray-50 px-4 py-2 rounded">
                                                    <div class="flex items-center">
                                                        <!-- Circle with color -->
                                                        <span class="w-4 h-4 rounded-full inline-block mr-2" style="background-color: {{ $child->color_code }}"></span>
                                                        <span class="font-medium text-gray-800">{{ $child->name }}</span>
                                                        <span class="ml-2 text-sm text-gray-500">({{ $child->description }})</span>
                                                    </div>
                                                    <div class="space-x-2">
                                                        <a href="{{ route('categories.edit', $child) }}" class="text-sm text-blue-600 hover:underline">Edit</a>
                                                        <button onclick="openModal('deleteModal-sub-{{ $child->id }}')" class="text-sm text-red-600 hover:underline">Delete</button>
                                                    </div>
                                                </li>

                                                <!-- Delete Modal for Subcategory -->
                                                <div id="deleteModal-sub-{{ $child->id }}" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center">
                                                    <div class="bg-white p-6 rounded-xl max-w-sm w-full shadow-lg text-center">
                                                        <h3 class="text-lg font-semibold mb-4 text-red-600">Delete subcategory?</h3>
                                                        <p class="text-gray-600 mb-6">This will delete "{{ $child->name }}" and cannot be undone.</p>
                                                        <form action="{{ route('categories.destroy', $child) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="flex justify-center space-x-4">
                                                                <button type="button" onclick="closeModal('deleteModal-sub-{{ $child->id }}')" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                                                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Delete Modal for Parent Category -->
                        <div id="deleteModal-{{ $parent->id }}" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center">
                            <div class="bg-white p-6 rounded-xl max-w-sm w-full shadow-lg text-center">
                                <h3 class="text-lg font-semibold mb-4 text-red-600">Delete category?</h3>
                                <p class="text-gray-600 mb-6">
                                    This will delete "{{ $parent->name }}"
                                    @if($parent->children->isNotEmpty())
                                        and all its {{ $parent->children->count() }} subcategories
                                    @endif
                                    permanently.
                                </p>
                                <form action="{{ route('categories.destroy', $parent) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="flex justify-center space-x-4">
                                        <button type="button" onclick="closeModal('deleteModal-{{ $parent->id }}')" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        const sortable = new Sortable(document.getElementById('sortable'), {
            animation: 200,
            ghostClass: 'bg-yellow-100',
            onEnd: function () {
                const items = [...document.querySelectorAll('#sortable > li')];
                const order = items.map((el, index) => ({
                    id: el.dataset.id,
                    position: index
                }));

                fetch("{{ route('categories.reorder') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ order })
                })
                    .then(res => res.json())
                    .then(data => console.log(data));
            }
        });

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('flex');
            document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('flex');
            document.getElementById(id).classList.add('hidden');
            document.body.style.overflow = ''; // Re-enable scrolling
        }
    </script>
</x-app-layout>
