@extends('layouts.landing')

@section('title', 'Digitalisasi Resto - Solusi Pesanan QR Code')

@section('content')
    {{-- ================= HERO SECTION ================= --}}
    <section class="relative z-10 px-6 md:px-16 pt-10 pb-24">
        {{-- Dekorasi Blob --}}
        <div class="blob w-80 h-80 bg-teal-300 -top-10 -left-20"></div>
        <div class="blob w-96 h-96 bg-blue-200 top-40 right-0"></div>
        <div class="blob w-72 h-72 bg-orange-200 bottom-0 left-1/3"></div>

        <div class="relative grid md:grid-cols-2 gap-12 items-center max-w-7xl mx-auto">
            {{-- Kiri: Teks --}}
            <div class="text-center md:text-left">
                <h1 class="text-3xl md:text-5xl font-extrabold leading-tight mb-5">
                    Pusing pelanggan rame tapi <span class="text-teal-600">antrean pesanan berantakan?</span>
                </h1>
                <p class="text-gray-600 text-base md:text-lg mb-8 leading-relaxed">
                    Digitalisasi sistem pesanan restoran & kafe tanpa ribet. Cukup scan QR Code meja!
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    <a href="{{ route('register') }}" class="bg-teal-500 hover:bg-teal-600 text-white font-semibold px-8 py-3.5 rounded-full shadow-xl shadow-teal-200 transition text-center">
                        Daftar Restoran Sekarang
                    </a>
                    <button type="button" onclick="openDemoModal()" class="bg-white text-teal-600 font-semibold px-8 py-3.5 rounded-full shadow-md border border-teal-100 transition text-center hover:bg-teal-50">
                        Lihat Demo Dashboard
                    </button>
                </div>

                {{-- Stats kecil --}}
                <div class="flex gap-8 mt-10 justify-center md:justify-start">
                    <div>
                        <p class="text-2xl font-bold text-teal-600">500+</p>
                        <p class="text-xs text-gray-500">Restoran Bergabung</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-blue-600">1Jt+</p>
                        <p class="text-xs text-gray-500">Pesanan Diproses</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-orange-500">4.9★</p>
                        <p class="text-xs text-gray-500">Rating Pengguna</p>
                    </div>
                </div>
            </div>

            {{-- Kanan: Ilustrasi --}}
            <div class="relative">
                <div class="relative bg-white/60 backdrop-blur rounded-3xl p-6 shadow-2xl border border-white/80">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-teal-100 to-teal-50 rounded-2xl p-4 flex flex-col items-center justify-center min-h-[220px]">
                            <div class="text-5xl mb-2">📱</div>
                            <div class="text-3xl">🧑‍💼</div>
                            <p class="text-xs text-center mt-2 text-gray-600 font-medium">Scan QR Code</p>
                        </div>
                        <div class="bg-gradient-to-br from-blue-100 to-blue-50 rounded-2xl p-4 flex flex-col items-center justify-center min-h-[220px]">
                            <div class="text-5xl mb-2">📊</div>
                            <div class="text-3xl">👩‍💼</div>
                            <p class="text-xs text-center mt-2 text-gray-600 font-medium">Kelola Pesanan</p>
                        </div>
                    </div>

                    <div class="absolute -top-4 -right-4 bg-white rounded-xl shadow-lg px-3 py-2 flex items-center gap-2 animate-bounce">
                        <span class="text-lg">✅</span>
                        <span class="text-xs font-semibold">Pesanan Masuk!</span>
                    </div>
                    <div class="absolute -bottom-4 -left-4 bg-white rounded-xl shadow-lg px-3 py-2 flex items-center gap-2">
                        <span class="text-lg">🍽️</span>
                        <span class="text-xs font-semibold">Menu Digital</span>
                    </div>
                </div>

                <div class="absolute -top-8 left-0 text-4xl">🌿</div>
                <div class="absolute top-1/3 -right-6 text-4xl">🍜</div>
                <div class="absolute -bottom-6 right-10 text-4xl">🍕</div>
                <div class="absolute top-10 -left-4 text-3xl">💡</div>
            </div>
        </div>
    </section>

    {{-- ================= FITUR UNGGULAN ================= --}}
    <section id="fitur" class="relative z-10 px-6 md:px-16 py-20 bg-white/50">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-14">
                <span class="text-teal-600 font-semibold text-sm uppercase tracking-wider">Fitur Unggulan</span>
                <h2 class="text-3xl md:text-4xl font-extrabold mt-2">Semua yang Restoran Anda Butuhkan</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-3xl p-8 shadow-xl shadow-teal-100/50 hover:-translate-y-2 transition border border-teal-50">
                    <div class="w-16 h-16 bg-teal-100 rounded-2xl flex items-center justify-center text-3xl mb-5">📲</div>
                    <h3 class="font-bold text-xl mb-2">Scan & Pesan Mandiri</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Katalog Menu Interaktif — pelanggan cukup scan QR di meja, langsung pesan tanpa antre.</p>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-xl shadow-blue-100/50 hover:-translate-y-2 transition border border-blue-50">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center text-3xl mb-5">🖥️</div>
                    <h3 class="font-bold text-xl mb-2">Pesanan Masuk Instan</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Proses Dapur Lebih Efisien — monitor dapur digital menampilkan order real-time.</p>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-xl shadow-orange-100/50 hover:-translate-y-2 transition border border-orange-50">
                    <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center text-3xl mb-5">📈</div>
                    <h3 class="font-bold text-xl mb-2">Laporan Penjualan Akurat</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Analisis Bisnis Mudah — dashboard lengkap dengan grafik dan rekap harian/bulanan.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= TESTIMONI ================= --}}
    <section id="testimoni" class="relative z-10 px-6 md:px-16 py-20">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-14">
                <span class="text-blue-600 font-semibold text-sm uppercase tracking-wider">Testimoni Pelanggan</span>
                <h2 class="text-3xl md:text-4xl font-extrabold mt-2">Dipercaya Ratusan Restoran</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @php
                    $testimoni = [
                        ['nama' => 'Budi', 'resto' => 'Kafe Kopi Kita', 'pesan' => 'Sangat terbantu! Antrean berkurang.', 'emoji' => '👨‍🍳', 'warna' => 'teal'],
                        ['nama' => 'Sari', 'resto' => 'Warung Bakso Maknyus', 'pesan' => 'Omzet naik karena efisiensi.', 'emoji' => '👩‍🍳', 'warna' => 'blue'],
                        ['nama' => 'Andi', 'resto' => 'Resto Padang Jaya', 'pesan' => 'Dashboard sangat mudah digunakan.', 'emoji' => '🧑‍🍳', 'warna' => 'orange'],
                    ];
                @endphp

                @foreach ($testimoni as $t)
                    <div class="bg-white rounded-3xl p-7 shadow-lg border border-gray-100">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-14 h-14 rounded-full bg-{{ $t['warna'] }}-100 flex items-center justify-center text-2xl">
                                {{ $t['emoji'] }}
                            </div>
                            <div>
                                <p class="font-bold">{{ $t['nama'] }}</p>
                                <p class="text-xs text-gray-500">{{ $t['resto'] }}</p>
                            </div>
                        </div>
                        <p class="text-gray-600 italic">"{{ $t['pesan'] }}"</p>
                        <div class="mt-3 text-yellow-400 text-sm">★★★★★</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= CARA MULAI ================= --}}
    <section id="cara-mulai" class="relative z-10 px-6 md:px-16 py-20 bg-white/50">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-14">
                <span class="text-orange-500 font-semibold text-sm uppercase tracking-wider">Cara Mulai</span>
                <h2 class="text-3xl md:text-4xl font-extrabold mt-2">Mulai dalam 3 Langkah Mudah</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @php
                    $steps = [
                        ['ikon' => '📝', 'judul' => 'Daftar Restoran Anda', 'desc' => 'Buat akun dalam 1 menit, tanpa biaya awal.'],
                        ['ikon' => '📋', 'judul' => 'Siapkan Menu Digital', 'desc' => 'Upload menu, harga, dan kategori dengan mudah.'],
                        ['ikon' => '🔲', 'judul' => 'Cetak & Tempel QR Meja', 'desc' => 'Generate QR Code unik untuk setiap meja.'],
                    ];
                @endphp

                @foreach ($steps as $i => $s)
                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto bg-gradient-to-br from-teal-400 to-teal-600 text-white rounded-3xl flex items-center justify-center text-4xl shadow-xl shadow-teal-200 mb-5">
                            {{ $s['ikon'] }}
                        </div>
                        <div class="text-xs font-bold text-teal-600 mb-1">LANGKAH {{ $i + 1 }}</div>
                        <h3 class="font-bold text-lg mb-2">{{ $s['judul'] }}</h3>
                        <p class="text-gray-500 text-sm">{{ $s['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- MODAL DEMO DASHBOARD --}}
    <div id="demo-modal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden relative">
            <button type="button" onclick="closeDemoModal()" class="absolute top-4 right-4 bg-white/80 hover:bg-white text-gray-800 w-10 h-10 rounded-full flex items-center justify-center text-2xl z-10 shadow-md transition">&times;</button>
            
            <div class="p-6">
                <h3 class="text-2xl font-extrabold text-center mb-2">Simulasi Dashboard Restoran</h3>
                <p class="text-center text-gray-500 mb-6">Begini tampilan sistem saat pemilik restoran masuk.</p>
                
                {{-- Ganti src dengan gambar dashboard aslimu --}}
                <div class="bg-gray-100 rounded-xl aspect-video flex flex-col items-center justify-center border-2 border-dashed border-gray-300">
                    <span class="text-6xl mb-4">🖥️</span>
                    <p class="text-gray-500 font-semibold">(Tempatkan Screenshot Dashboard di sini)</p>
                    <p class="text-xs text-gray-400 mt-2">Atau masukkan link video YouTube</p>
                </div>
                
                <div class="mt-6 flex justify-center">
                    <a href="{{ route('register') }}" wire:navigate class="bg-teal-500 hover:bg-teal-600 text-white font-bold px-8 py-3 rounded-xl shadow-md transition">
                        Coba Sekarang (Gratis)
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openDemoModal() {
        document.getElementById('demo-modal').classList.remove('hidden');
        document.getElementById('demo-modal').classList.add('flex');
    }

    function closeDemoModal() {
        document.getElementById('demo-modal').classList.add('hidden');
        document.getElementById('demo-modal').classList.remove('flex');
    }
</script>
@endpush