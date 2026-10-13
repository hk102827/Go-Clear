<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerProfileController extends Controller
{
    // ✅ GET /api/customer/profile
    public function getProfile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'full_name'    => $user->name,
                'phone_number' => $user->phone,
                'email'        => $user->email,
                'home_address' => $user->address,
            ]
        ]);
    }

    // ✅ PUT /api/customer/profile
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'full_name'    => 'nullable|string|max:150',
            'phone_number' => 'nullable|string|max:30|unique:users,phone,' . $user->id,
            'email'        => 'nullable|email|max:150|unique:users,email,' . $user->id,
            'home_address' => 'nullable|string|max:255',
        ]);

        $user->update([
            'name'    => $validated['full_name'] ?? $user->name,
            'phone'   => $validated['phone_number'] ?? $user->phone,
            'email'   => $validated['email'] ?? $user->email,
            'address' => $validated['home_address'] ?? $user->address,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => [
                'full_name'    => $user->name,
                'phone_number' => $user->phone,
                'email'        => $user->email,
                'home_address' => $user->address,
            ]
        ]);
    }

    // ✅ POST /api/customer/change-password
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.']
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.'
        ]);
    }
}
