@extends('layouts.app')

@section('title', 'Pengaturan Toko - ' . $restaurant->name)

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-extrabold">Pengaturan Toko</h2>
    <p class="text-sm text-gray-500">Kelola profil, jam operasional, dan pembayaran QRIS Anda</p>
</div>

@if (session('success'))
    <div class="mb-5 bg-teal-50 border border-teal-200 text-teal-700 text-sm rounded-xl px-4 py-3">
        ✅ {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-5 bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
    </div>
@endif

{{-- FORM PROFIL --}}
<form method="POST" action="{{ route('resto.settings.profile') }}">
    @csrf

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <h3 class="font-bold text-sm mb-4">Profil Restoran</h3>
        <div class="space-y-4">
            {{-- Logo --}}
            <div>
                <label class="block text-sm font-semibold mb-2">Logo / Foto Profil</label>
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded-xl bg-gray-100 flex items-center justify-center overflow-hidden">
                        @if ($restaurant->logo && file_exists(storage_path('app/public/' . $restaurant->logo)))
                            <img id="logo-preview" src="{{ asset('storage/' . $restaurant->logo) }}" class="w-full h-full object-cover">
                        @else
                            <span id="logo-placeholder" class="text-3xl">🏪</span>
                        @endif
                    </div>
                    <button type="button" onclick="openLogoModal()" class="cursor-pointer bg-teal-50 hover:bg-teal-100 text-teal-600 font-semibold px-4 py-2.5 rounded-lg text-sm transition">
                        Ganti Logo
                    </button>
                    <input type="hidden" name="logo_file" id="logo-file-data">
                </div>
            </div>

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-semibold mb-2">Nama Restoran</label>
                <input type="text" name="name" value="{{ old('name', $restaurant->name) }}" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 outline-none text-sm">
            </div>

            {{-- Alamat --}}
            <div>
                <label class="block text-sm font-semibold mb-2">Alamat / Deskripsi</label>
                <textarea name="address" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 outline-none text-sm resize-none">{{ old('address', $restaurant->address) }}</textarea>
            </div>

            {{-- Nomor Telepon (TAMBAHKAN INI) --}}
            <div class="mt-4">
                <label class="block text-sm font-semibold mb-2">Nomor Telepon Restoran</label>
                <input type="text" name="phone" value="{{ old('phone', $restaurant->phone) }}" 
                    placeholder="Contoh: 0812-3456-7890"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 outline-none text-sm">
                <p class="text-xs text-gray-400 mt-1">Akan ditampilkan di bagian bawah halaman menu pelanggan.</p>
            </div>

            <!-- Tambahkan kode ini di bawah input Nomor Telepon -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-bold mb-2">Nomor WhatsApp</label>
                    <input type="text" name="whatsapp_link" value="{{ old('whatsapp_link', $restaurant->whatsapp_link ?? '') }}" 
                        placeholder="Contoh: 0856251961" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 outline-none text-sm bg-gray-50/50">
                    <p class="text-[10px] text-gray-400 mt-1">Cukup ketik nomornya saja.</p>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-2">Username Instagram</label>
                    <input type="text" name="instagram_link" value="{{ old('instagram_link', $restaurant->instagram_link ?? '') }}" 
                        placeholder="Contoh: gacoan_official" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 outline-none text-sm bg-gray-50/50">
                    <p class="text-[10px] text-gray-400 mt-1">Tanpa tanda @.</p>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-2">Username TikTok</label>
                    <input type="text" name="tiktok_link" value="{{ old('tiktok_link', $restaurant->tiktok_link ?? '') }}" 
                        placeholder="Contoh: gacoan_official" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 outline-none text-sm bg-gray-50/50">
                    <p class="text-[10px] text-gray-400 mt-1">Tanpa tanda @.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <h3 class="font-bold text-sm mb-4">Jam Operasional & Status</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-2">Jam Buka</label>
                <input type="time" name="open_time" value="{{ old('open_time', $restaurant->open_time ?? '08:00') }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">Jam Tutup</label>
                <input type="time" name="close_time" value="{{ old('close_time', $restaurant->close_time ?? '22:00') }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">Status Toko</label>
                <select name="status" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm">
                    <option value="open" {{ ($restaurant->status ?? 'open') === 'open' ? 'selected' : '' }}>Buka</option>
                    <option value="closed" {{ ($restaurant->status ?? '') === 'closed' ? 'selected' : '' }}>Tutup</option>
                </select>
            </div>
        </div>
    </div>

    <div class="flex justify-end mb-8">
        <button type="submit" class="bg-teal-500 hover:bg-teal-600 text-white font-bold px-6 py-3 rounded-xl shadow-md shadow-teal-200 transition">
            Simpan Perubahan
        </button>
    </div>
</form>

{{-- QRIS --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
    <h3 class="font-bold text-sm mb-4">QRIS Toko</h3>
    <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
        {{-- Gambar QRIS --}}
        <div class="w-40 h-40 rounded-xl bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
            @if ($restaurant->qris_image && file_exists(storage_path('app/public/' . $restaurant->qris_image)))
                <img src="{{ asset('storage/' . $restaurant->qris_image) }}" class="w-full h-full object-contain">
            @else
                <span class="text-3xl">📱</span>
            @endif
        </div>

        {{-- Info & Tombol --}}
        <div class="flex-1">
            <div class="mb-4">
                <p class="text-xs text-gray-500 mb-1">Nama Pemilik QRIS:</p>
                <p class="font-bold text-lg text-gray-800">{{ $restaurant->qris_owner_name ?? 'Belum diatur' }}</p>
            </div>
            
            <div class="flex gap-2">
                <button type="button" onclick="openPinModal()" class="bg-teal-50 hover:bg-teal-100 text-teal-600 font-semibold px-4 py-2.5 rounded-lg text-sm transition">
                    Ganti Foto QRIS
                </button>
                <button type="button" onclick="openPinModal(true)" class="bg-orange-50 hover:bg-orange-100 text-orange-600 font-semibold px-4 py-2.5 rounded-lg text-sm transition">
                    Ubah Nama QRIS
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CROP LOGO --}}
<div id="logo-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="font-bold text-lg">Crop Logo</h3>
            <button type="button" onclick="closeLogoModal()" class="text-gray-400 text-2xl leading-none">×</button>
        </div>
        <div class="bg-gray-900 p-4">
            <div class="w-full h-80 overflow-hidden">
                <img id="crop-logo-image" src="" class="block max-w-full">
            </div>
        </div>
        <div class="px-6 py-3 bg-teal-50 text-teal-700 text-xs text-center">
            💡 Geser & zoom foto untuk mengatur logo. Rasio 1:1
        </div>
        <div class="p-4 mb-2">
            <input type="file" id="logo-input-file" accept="image/*" onchange="loadLogoToCrop(event)" class="text-xs">
        </div>
        <div class="flex justify-end gap-2 px-6 py-4 border-t">
            <button type="button" onclick="closeLogoModal()" class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm">Batal</button>
            <button type="button" onclick="applyLogo()" class="px-5 py-2.5 rounded-xl bg-teal-500 text-white font-semibold text-sm">Terapkan</button>
        </div>
    </div>
</div>

{{-- MODAL PIN & UPLOAD QRIS --}}
<div id="pin-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
        <h3 class="font-bold text-lg mb-2">Masukkan PIN Keamanan</h3>
        <p class="text-sm text-gray-500 mb-4">PIN 6 digit diperlukan untuk mengubah QRIS.</p>

        <form method="POST" action="{{ route('resto.settings.qris') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="qris_file" id="qris-file-data">

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">PIN (6 Digit)</label>
                <input type="password" name="pin" maxlength="6" required placeholder="••••••"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 outline-none text-center text-xl tracking-widest">
                <div class="text-right mt-1 mb-4">
                    <button type="button" onclick="openPinResetModal()" class="text-xs text-teal-600 font-semibold hover:underline">Lupa PIN?</button>
                </div>
            </div>

            {{-- Input Nama Pemilik QRIS --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Nama Pemilik QRIS</label>
                <input type="text" name="qris_owner_name" value="{{ $restaurant->qris_owner_name }}" 
                    placeholder="Contoh: Warung Gacoan / Budi"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 outline-none text-sm">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Upload Foto QRIS Baru (Opsional)</label>
                <input type="file" id="qris-input" accept="image/*" onchange="previewQris(event)"
                    class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-teal-50 file:text-teal-600">
            </div>

            <div id="qris-preview-container" class="hidden mb-4">
                <img id="qris-preview" class="w-32 h-32 object-contain rounded-lg border">
            </div>

            <div class="flex gap-2">
                <button type="button" onclick="closePinModal()" class="flex-1 px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm">Batal</button>
                <button type="submit" class="flex-1 px-5 py-2.5 rounded-xl bg-teal-500 text-white font-semibold text-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL RESET PIN --}}
<div id="pin-reset-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[70] hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
        <h3 class="font-bold text-lg mb-2">Reset PIN</h3>
        <p class="text-sm text-gray-500 mb-4">Masukkan OTP yang dikirim ke email Anda dan PIN baru.
        <span class="text-orange-500 font-semibold">Jika tidak menemukannya di Inbox, silakan cek folder Spam/Junk.</span></p>
        
        <div id="pin-reset-step-1">
            <button type="button" onclick="sendPinOtp()" id="btn-send-otp" 
                data-url="{{ route('resto.settings.sendPinOtp') }}" 
                data-reset-url="{{ route('resto.settings.resetPin') }}" 
                class="w-full bg-teal-50 text-teal-600 font-bold py-3 rounded-xl mb-4">
                Kirim OTP ke Email
            </button>
        </div>

        <div id="pin-reset-step-2" class="hidden space-y-4">
            <div>
                <label class="block text-sm font-semibold mb-2">Kode OTP</label>
                <input type="text" id="reset-otp" maxlength="6" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-center tracking-widest" placeholder="123456">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">PIN Baru (6 Digit)</label>
                <input type="password" id="reset-pin" maxlength="6" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-center tracking-widest" placeholder="••••••">
            </div>
            <button type="button" onclick="submitResetPin()" class="w-full bg-teal-500 text-white font-bold py-3 rounded-xl shadow-md">Simpan PIN Baru</button>
        </div>
        
        <button type="button" onclick="closePinResetModal()" class="w-full mt-3 px-4 py-2 text-gray-500 font-semibold text-sm">Batal</button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<!-- Logika JS sudah dipindah ke public/js/resto.js agar tidak bentrok dengan Livewire -->
@endpush