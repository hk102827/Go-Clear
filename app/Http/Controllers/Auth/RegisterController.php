<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DriverProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:customer,driver',
            'address' => 'nullable|string|max:500',
            'postcode' => 'nullable|string|max:20',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = 'active';

        $user = User::create($validated);

        // Create driver profile if role is driver
        if ($user->isDriver()) {
            DriverProfile::create(['user_id' => $user->id]);
        }

        // Login the user
        Auth::login($user);

        // Redirect based on role
        if ($user->isDriver()) {
            return redirect()->route('admin.driver.documents.upload')->with('success', 'Welcome! Please complete your profile setup.');
        } else {
return redirect('/login')->with('success', 'Account created successfully!');
        }
    }
}