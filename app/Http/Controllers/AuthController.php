<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'pin' => 'required|digits:4',
        ]);

        $user = User::where('username', $request->username)
                    ->where('pin', $request->pin)
                    ->first();

        if ($user) {
            Auth::login($user);
            // ✅ Alert sukses
            Alert::success('Login Berhasil!','Selamat datang, ' . $user->nama . '!');
            return redirect()->route('dashboard');
        }

        // ❌ Alert error
        Alert::error('Gagal!', 'Username atau PIN salah.');
        return redirect()->back()->withInput();
    }

    public function logout()
    {
        Auth::logout();
        Alert::success('Berhasil!', 'Anda telah logout.');
        return redirect()->route('login');
    }
}
