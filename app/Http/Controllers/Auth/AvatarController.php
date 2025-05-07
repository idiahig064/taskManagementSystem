<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use function Pest\Laravel\delete;

class AvatarController extends Controller
{
    /**
     * Update the user's avatar.
     */
    public function update(Request $request): RedirectResponse
    {
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Generate a unique name for the image
            $imageName = round(microtime(true) * 1000).'.'.$request->image->extension();
            $request->image->move(public_path('images/avatars'), $imageName);

            $user = $request->user();

            // Delete the previous avatar if it is not the default one
            if ($user->avatar_path && $user->avatar_path !== 'images/avatars/default.png') {
                \Illuminate\Support\Facades\File::delete(public_path($user->avatar_path));
            }

            $user->avatar_path = 'images/avatars/'.$imageName;
            $user->save();

            return back()->with('status', 'avatar-updated');
        }

        return back()->withErrors(['image' => __('No image was uploaded.')]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Delete the previous avatar if it is not the default one
        if ($user->avatar_path && $user->avatar_path !== 'images/avatars/default.png') {
            \Illuminate\Support\Facades\File::delete(public_path($user->avatar_path));
        }

        // Reset the avatar path to the default image
        $user->avatar_path = 'images/avatars/default.png';
        $user->save();

        return back()->with('status', 'avatar-deleted');
    }
}
