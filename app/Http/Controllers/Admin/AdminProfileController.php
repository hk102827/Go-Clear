<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\User;


class AdminProfileController extends Controller
{
    /**
     * Show edit form
     */
    public function edit()
    {
        $admin = Auth::user();
        return view('admin.account.edit', compact('admin'));
    }

    /**
     * Update admin profile and possibly password.
     */
public function update(Request $request)
{
    $admin = Auth::user();

    $rules = [
        'name' => ['required','string','max:255'],
        'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($admin->id)],
        'profile_image' => ['nullable','image','mimes:jpeg,png,jpg','max:2048'],
        // password rules validated conditionally below
    ];

    $validated = $request->validate($rules);

    // FLAG to indicate whether to remove old image
    $shouldDeleteOld = false;

    // 1) If user requested to delete image explicitly (checkbox)
    if ($request->filled('delete_image') && $admin->profile_image) {
        // mark for deletion and clear DB value
        $shouldDeleteOld = true;
        $validated['profile_image'] = null;
    }

    // 2) If a new file uploaded, store it and set new path
    if ($request->hasFile('profile_image')) {
        // store new file first (so we do not lose old one if store fails)
        $path = $request->file('profile_image')->store('admins', 'public'); // e.g. admins/abc.jpg
        $validated['profile_image'] = $path;

        // after storing new file, delete the old file (if exists and not already same path)
        if ($admin->profile_image && $admin->profile_image !== $path) {
            if (Storage::disk('public')->exists($admin->profile_image)) {
                Storage::disk('public')->delete($admin->profile_image);
            }
        }
    } else {
        // If no new upload, but user checked delete_image, delete the old file
        if ($shouldDeleteOld && $admin->profile_image) {
            if (Storage::disk('public')->exists($admin->profile_image)) {
                Storage::disk('public')->delete($admin->profile_image);
            }
            // ensure DB will be null (already set above)
        }
    }

    // Password change flow: only if password provided
    $newPassword = $request->input('password');
    if ($newPassword) {
        // require current password
        if (! $request->filled('current_password')) {
            return back()->withErrors(['current_password' => 'Current password is required to change password.'])->withInput();
        }

        if (! Hash::check($request->input('current_password'), $admin->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        // validate new password
        $request->validate([
            'password' => ['required','string','min:8','confirmed'],
        ]);

        $validated['password'] = Hash::make($newPassword);
    }

    // Update fields: fill only validated keys
    $admin->fill($validated);
    $admin->save();

    return back()->with('success', 'Profile updated successfully.');
}

public function deleteAvatar(Request $request)
{
    $admin = Auth::user();

    if ($admin->profile_image && Storage::disk('public')->exists($admin->profile_image)) {
        Storage::disk('public')->delete($admin->profile_image);
    }

    $admin->profile_image = null;
    $admin->save();

    return back()->with('success', 'Profile image removed.');
}

}
