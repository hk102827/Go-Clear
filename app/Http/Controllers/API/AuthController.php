<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DriverProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'phone'    => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:customer,driver',

            // Driver-only fields
            'address'  => 'required_if:role,driver|nullable|string',
            'postcode' => 'required_if:role,driver|nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Driver profile only for driver
        if ($user->role === 'driver') {
            DriverProfile::create([
                'user_id' => $user->id,
                'address' => $validated['address'] ?? null,
                'postcode'=> $validated['postcode'] ?? null,
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 201);
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->status === 'blocked') {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been blocked'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    // public function profile(Request $request)
    // {
    //     $user = $request->user();
        
    //     if ($user->isDriver()) {
    //         $user->load('driverProfile');
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'data' => $user
    //     ]);
    // }

    // public function updateProfile(Request $request)
    // {
    //     $user = $request->user();

    //     $validated = $request->validate([
    //         'name' => 'sometimes|string|max:255',
    //         'phone' => 'sometimes|string|unique:users,phone,' . $user->id,
    //         'address' => 'nullable|string',
    //         'postcode' => 'nullable|string',
    //         'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //     ]);

    //     if ($request->hasFile('profile_image')) {
    //         $validated['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
    //     }

    //     $user->update($validated);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Profile updated successfully',
    //         'data' => $user
    //     ]);
    // }

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
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'full_name'    => 'nullable|string|max:150',
            'phone_number' => 'nullable|string|max:30',
            'email'        => 'nullable|email|max:150',
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
            'message' => 'Profile updated successfully.'
        ]);
    }
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.'
        ]);
    }
}