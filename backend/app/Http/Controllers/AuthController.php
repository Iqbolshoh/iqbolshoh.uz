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

        // The checkbox is ticked by default on the form, so this is normally
        // true; unticking it on a shared computer still opts out.
        $rememberUser = $request->boolean('remember');

        // Laravel's own default is 576000 minutes (400 days), which is far
        // longer than anyone intends. 30 days: long enough that nobody
        // re-types a password during ordinary use, short enough that a
        // session forgotten on someone else's machine dies within a month.
        Auth::guard('web')->setRememberDuration(60 * 24 * 30);

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
