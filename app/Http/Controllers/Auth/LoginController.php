<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Only active users allowed (optional)
        $credentials = $request->only('email', 'password');
        $credentials['active'] = 1; // make sure users table has 'active' column

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials or unauthorized account.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

public function showLoginForm()
{
    // If user is already logged in, redirect them with a notification
    if (auth()->check()) {
        if (auth()->user()->is_admin) {
            return redirect()->route('dashboard')->with('message', 'You are already logged in as Admin!');
        } else {
            return redirect('/')->with('message', 'You are already logged in!');
        }
    }

    // Otherwise, show the login form
    return view('auth.login');
}


}
