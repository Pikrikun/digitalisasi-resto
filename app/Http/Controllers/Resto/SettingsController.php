<?php

namespace App\Http\Controllers\Resto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class SettingsController extends Controller
{
    public function index()
    {
        $restaurant = Auth::user()->restaurant;
        return view('resto.settings.index', compact('restaurant'));
    }

    public function updateProfile(Request $request)
    {
        $restaurant = Auth::user()->restaurant;

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'address'    => ['nullable', 'string', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'open_time'  => ['required'],
            'close_time' => ['required'],
            'status'     => ['required', 'in:open,closed'],
            'logo_file'  => ['nullable', 'string'],
            'whatsapp_link'  => ['nullable', 'string'],
            'instagram_link' => ['nullable', 'string'],
            'tiktok_link'    => ['nullable', 'string'],
        ]);

        if ($request->filled('logo_file')) {
            if ($restaurant->logo && Storage::disk('public')->exists($restaurant->logo)) {
                Storage::disk('public')->delete($restaurant->logo);
            }
            $data['logo'] = $this->saveBase64Image($request->logo_file, 'logos');
        }
        unset($data['logo_file']);

        $restaurant->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updateQris(Request $request)
    {
        $restaurant = Auth::user()->restaurant;

        // Batasi percobaan PIN
        $key = 'qris-pin:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['pin' => 'Terlalu banyak percobaan PIN salah. Coba lagi dalam 1 menit.']);
        }

        $request->validate([
            'pin'      => ['required', 'digits:6'],
            'qris_owner_name' => ['nullable', 'string', 'max:100'],
            'qris_file'=> ['nullable', 'string'],
        ]);

        // Cek PIN (Menggunakan Hash)
        if ($restaurant->security_pin) {
            if (!Hash::check($request->pin, $restaurant->security_pin)) {
                RateLimiter::hit($key, 60);
                return back()->withErrors(['pin' => 'PIN Keamanan Salah!']);
            }
            RateLimiter::clear($key);
        } else {
            // Set PIN baru (di-hash)
            $restaurant->security_pin = Hash::make($request->pin);
        }

        if ($request->filled('qris_owner_name')) {
            $restaurant->qris_owner_name = $request->qris_owner_name;
        }

        if ($request->filled('qris_file')) {
            if ($restaurant->qris_image && Storage::disk('public')->exists($restaurant->qris_image)) {
                Storage::disk('public')->delete($restaurant->qris_image);
            }
            $restaurant->qris_image = $this->saveBase64Image($request->qris_file, 'qris');
        }

        $restaurant->save();

        return back()->with('success', 'Data QRIS berhasil diperbarui.');
    }

    private function saveBase64Image($dataUrl, $folder = 'misc')
    {
        $parts = explode(',', $dataUrl);
        if (count($parts) !== 2) return null;

        $imageData = base64_decode($parts[1]);
        if ($imageData === false) return null;

        $filename = $folder . '/' . Str::random(20) . '.jpg';
        Storage::disk('public')->put($filename, $imageData);

        return $filename;
    }

    public function sendPinOtp()
    {
        $restaurant = Auth::user()->restaurant;
        $user = Auth::user();

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        session(['pin_otp' => $otp, 'pin_otp_expires' => now()->addMinutes(10)]);

        $body = "Halo {$user->name},\n\nKode OTP untuk mereset PIN QRIS Restoran {$restaurant->name} Anda adalah:\n\n{$otp}\n\nKode ini berlaku 10 menit. Jangan bagikan ke siapapun.";

        try {
            \Illuminate\Support\Facades\Mail::raw($body, function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Kode OTP Reset PIN - Digitalisasi Resto');
            });
            return response()->json(['success' => true, 'message' => 'OTP terkirim ke email Anda.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal kirim email: ' . $e->getMessage()]);
        }
    }

    public function resetPin(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
            'new_pin' => ['required', 'digits:6'],
        ]);

        $savedOtp = session('pin_otp');
        $expires = session('pin_otp_expires');

        if (!$savedOtp || !$expires || now()->greaterThan($expires)) {
            return response()->json(['success' => false, 'message' => 'OTP sudah kedaluwarsa. Minta ulang.']);
        }

        if ($request->otp !== $savedOtp) {
            return response()->json(['success' => false, 'message' => 'Kode OTP salah.']);
        }

        $restaurant = Auth::user()->restaurant;
        $restaurant->update(['security_pin' => Hash::make($request->new_pin)]);

        session()->forget(['pin_otp', 'pin_otp_expires']);

        return response()->json(['success' => true, 'message' => 'PIN berhasil direset.']);
    }
}