<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle the incoming login request.
     */
    public function login(Request $request)
    {
        // Validate user credentials
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check if user wants to be remembered
        $rememberUser = $request->boolean('remember');

        // Attempt to authenticate the user
        if (Auth::attempt($credentials, $rememberUser)) {
            $request->session()->regenerate();

            return redirect()
                ->route('dashboard.index')
                ->with('success', 'Muvaffaqiyatli kirdingiz!');
        }

        return back()->withErrors([
            'email' => 'Email yoki parol noto\'g\'ri.',
        ])->onlyInput('email');
    }

    /**
     * Handle the logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect with translated logout message
        return redirect('/')->with('success', 'Tizimdan muvaffaqiyatli chiqdingiz.');
    }
}
