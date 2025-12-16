<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        // If already authenticated and admin, redirect to admin dashboard
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    // Handle login submission
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        // Optional 'remember' flag
        $remember = $request->boolean('remember');

        // Attempt to fetch user first to check role & status
        $user = \App\Models\User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Credentials do not match our records.'])->withInput();
        }

        // Ensure user is an admin
        if ($user->role !== 'admin') {
            return back()->withErrors(['email' => 'You do not have permission to access the admin panel.'])->withInput();
        }

        // Optionally check status if you have a status field
        if (isset($user->status) && $user->status === 'blocked') {
            return back()->withErrors(['email' => 'Your account is blocked.'])->withInput();
        }

        // Attempt to authenticate
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['email' => 'The provided credentials are incorrect.'])->withInput();
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }


     // ==========================
    // FORGOT PASSWORD
    // ==========================
    public function showLinkRequestForm()
    {
        return view('auth.email'); // create this view
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (! $user || $user->role !== 'admin') {
            return back()->withErrors(['email' => 'This email is not registered as an admin.']);
        }

        $status = Password::sendResetLink($request->only('email'));

        // Log the status for debugging
        \Log::info('Password reset status for admin email '.$request->email.': '.$status);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }


    // ==========================
    // RESET PASSWORD
    // ==========================
    public function showResetForm(Request $request, $token)
    {
        return view('auth.password_reset', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Make sure only admin accounts can be reset
        $user = \App\Models\User::where('email', $request->email)->first();

        if (! $user || $user->role !== 'admin') {
            return back()->withErrors(['email' => 'This email is not registered as an admin.']);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password)
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('admin.login')->with('status', 'Password reset successful.');
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
