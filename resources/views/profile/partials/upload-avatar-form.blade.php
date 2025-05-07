<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile photo') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Upload an image to convert into your avatar.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('avatar.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="avatar" :value="__('Avatar')" class="mb-4" />
            <img src="{{ asset($user->avatar_path) }}" alt="{{ $user->name }} profile picture" class="rounded-full text-gray-400 dark:text-gray-400  focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 h-20 w-20 text-center object-cover my-4"/>
            <x-text-input id="avatar" name="image" type="file" class="mt-4 block w-full border-0" required />
            <x-input-error class="mt-2" :messages="$errors->get('image')" />

            @if (session('status') === 'invalid-avatar')
                <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                    {{ __('This image format is invalid.') }}
                </p>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'avatar-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif

            <button type="button" onclick="openModal()" class="px-4 py-2 bg-red-600 text-white rounded-md">
                {{ __('Delete Photo') }}
            </button>
        </div>
    </form>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Confirm Deletion') }}</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">{{ __('Are you sure you want to delete your profile photo? This action cannot be undone.') }}</p>
            <div class="flex justify-end gap-4">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md">{{ __('Cancel') }}</button>
                <form method="post" action="{{ route('avatar.destroy') }}">
                    @csrf
                    @method('delete')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md">{{ __('Delete') }}</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</section>
