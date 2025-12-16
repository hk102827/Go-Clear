<?php

// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class LoginController extends Controller
// {
//     public function showLoginForm()
//     {
//         return view('auth.login');
//     }

//     public function login(Request $request)
//     {
//         $credentials = $request->validate([
//             'email' => 'required|string',
//             'password' => 'required|string',
//         ]);

//         // Check if input is email or phone
//         $field = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

//         if (Auth::attempt([$field => $credentials['email'], 'password' => $credentials['password']])) {
//             $request->session()->regenerate();

//             $user = Auth::user();

//             // Check if user is blocked
//             if ($user->status === 'blocked') {
//                 Auth::logout();
//                 return back()->withErrors(['email' => 'Your account has been blocked.']);
//             }

//             // Redirect based on role
//             if ($user->isAdmin()) {
//                 return redirect()->intended('/admin/dashboard');
//             } elseif ($user->isDriver()) {
//                 // Check if driver needs verification
//                 if (!$user->driverProfile->isVerified()) {
//                     return redirect()->route('driver.verification');
//                 }
//                 return redirect()->intended('/driver/dashboard');
//             } else {
//                 return redirect()->intended('/customer/dashboard');
//             }
//         }

//         return back()->withErrors([
//             'email' => 'The provided credentials do not match our records.',
//         ])->onlyInput('email');
//     }

//     public function logout(Request $request)
//     {
//         Auth::logout();
//         $request->session()->invalidate();
//         $request->session()->regenerateToken();
        
//         return redirect('/login');
//     }
// }