<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        if (RateLimiter::tooManyAttempts('register:' . $request->ip(), 3)) {
            return back()->withErrors(['email' => 'Terlalu banyak percobaan pendaftaran. Silakan coba lagi nanti.']);
        }
        RateLimiter::hit('register:' . $request->ip(), 60); // Reset setiap 60 detik

        $data = $request->validate([
            'restaurant_name' => ['required', 'string', 'max:100'],
            'name'            => ['required', 'string', 'max:100'],
            'email'           => ['required', 'email', 'unique:users,email'],
            'password'        => ['required', 'min:8', 'confirmed'],
        ]);

        DB::transaction(function () use ($data) {
            // Buat user owner
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => 'owner',
            ]);

            // Buat restoran
            $restaurant = Restaurant::create([
                'owner_id' => $user->id,
                'name'     => $data['restaurant_name'],
                'slug'     => Str::slug($data['restaurant_name']) . '-' . Str::lower(Str::random(4)),
            ]);

            // Hubungkan user ke restoran
            $user->update(['restaurant_id' => $restaurant->id]);

            Auth::login($user);
        });

        return redirect('/resto')->with('success', 'Selamat! Restoran Anda berhasil didaftarkan.');
    }
}