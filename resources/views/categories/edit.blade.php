<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-white leading-tight">
            Edit Category
        </h2>
    </x-slot>

    <div class="py-10 max-w-3xl mx-auto px-4">
        <form action="{{ route('categories.update', $category) }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-6">
            @csrf
            @method('PUT')

            <!-- Parent Category -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Color Code</label>
                <input type="color" name="color_code" value="{{ old('color_code', $category->color_code) }}" required
                       class="mt-1 block w-16 h-10 p-0 border-0 shadow-sm cursor-pointer">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="3" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">{{ old('description', $category->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Parent Category</label>
                <select name="parent_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="0" {{ $category->parent_id == 0 ? 'selected' : '' }}>None</option>
                    @foreach ($allCategories as $cat)
                        @if ($cat->id !== $category->id)
                            <option value="{{ $cat->id }}" {{ $category->parent_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- Save Button -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <a href="{{ route('categories.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-black rounded-lg shadow hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                    Update Category
                </button>
            </div>
        </form>

        @if ($category->children->isNotEmpty())
            <div class="mt-10">
                <h3 class="text-lg font-semibold mb-4">Subcategories</h3>
                <ul class="space-y-4">
                    @foreach ($category->children as $child)
                        <li class="bg-gray-100 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="font-medium text-gray-800">{{ $child->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $child->description }}</div>
                                </div>
                                <div class="space-x-2">
                                    <a href="{{ route('categories.edit', $child) }}"
                                       class="text-sm text-blue-600 hover:underline">Edit</a>
                                    <form action="{{ route('categories.destroy', $child) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-sm text-red-600 hover:underline"
                                                onclick="return confirm('Are you sure you want to delete this subcategory?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</x-app-layout>
