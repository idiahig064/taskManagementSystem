<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-white leading-tight">
            Create New Category
        </h2>
    </x-slot>

    <div class="py-10 max-w-3xl mx-auto px-4">
        <form action="{{ route('categories.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">
                @error('name') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
            </div>

            <!-- Color Code -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                <div class="flex items-center space-x-4 gap-3">
                    <input type="color" name="color_code" value="{{ old('color_code', '#3b82f6') }}" required
                           class="w-16 h-10 p-0 border-0 rounded cursor-pointer shadow-sm">
                    <span class="text-sm text-gray-500">Click to change color</span>
                </div>
                @error('color_code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="3" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200">{{ old('description') }}</textarea>
                @error('description') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
            </div>

            <!-- Parent Category -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Parent Category</label>
                <select name="parent_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="0">None</option>
                    @foreach ($allCategories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('parent_id') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
            </div>

            <!-- Save Button -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <a href="{{ route('categories.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-black rounded-lg shadow hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                    Create Category
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
