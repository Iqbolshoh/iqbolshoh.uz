<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
        ]);

        $user->update($data);

        return back()->with('success', 'Profil muvaffaqiyatli yangilandi.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Joriy parol noto\'g\'ri.'])->withInput();
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Parol muvaffaqiyatli o\'zgartirildi.');
    }
}
