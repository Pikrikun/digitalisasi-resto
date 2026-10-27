<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Karena sistem hanya punya Owner, langsung arahkan ke /resto
            // Jika nanti ada role lain (misal master), bisa ditambahkan lagi
            if (Auth::user()->role === 'owner') {
                return redirect('/resto');
            }

            // Jika role tidak dikenal (misal data lama), lempar ke halaman utama
            return redirect('/');
        }
        
        return back()->withErrors([
            'email' => 'Email atau kata sandi salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        foreach ($request->cookies as $name => $value) {
            Cookie::queue(Cookie::forget($name));
        }

        return redirect('/login')
            ->with('success', 'Anda telah berhasil keluar.')
            ->header('Clear-Site-Data', '"cache", "cookies", "storage"');
    }
}