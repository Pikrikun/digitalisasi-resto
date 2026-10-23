<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    // Halaman form input email
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // Kirim OTP ke email
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak terdaftar di sistem kami.']);
        }

        // Buat OTP 6 digit
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Simpan / update OTP
        PasswordResetOtp::updateOrCreate(
            ['email' => $request->email],
            ['otp' => $otp, 'expires_at' => now()->addMinutes(10)]
        );

        // Kirim email
        $body = "Halo {$user->name},\n\nKode OTP untuk mereset password Anda adalah:\n\n{$otp}\n\nKode ini berlaku 10 menit. Jangan bagikan ke siapapun.";
        
        try {
            Mail::raw($body, function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Kode OTP Reset Password - Digitalisasi Resto');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Gagal mengirim email. Cek konfigurasi SMTP. Error: ' . $e->getMessage()]);
        }

        // Simpan email di session untuk verifikasi
        session(['reset_email' => $request->email]);

        return redirect()->route('password.verify-form')->with('success', 'Kode OTP telah dikirim ke email Anda. Cek inbox atau folder spam.');
    }

    // Halaman verifikasi OTP
    public function showVerifyForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.verify-otp');
    }

    // Verifikasi OTP & reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        $email = session('reset_email');
        if (!$email) {
            return redirect()->route('password.request');
        }

        $record = PasswordResetOtp::where('email', $email)->first();

        if (!$record || $record->otp !== $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP salah.']);
        }

        if ($record->expires_at->isPast()) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kedaluwarsa. Silakan minta ulang.']);
        }

        // Update password
        $user = User::where('email', $email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        // Hapus OTP
        $record->delete();
        session()->forget('reset_email');

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login dengan password baru.');
    }
}