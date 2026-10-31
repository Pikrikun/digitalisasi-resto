@extends('layouts.landing')

@section('title', 'Digitalisasi Resto - Solusi Pesanan QR Code')

@section('content')

    {{-- ================= HERO SECTION ================= --}}
    <section class="relative pt-32 pb-20 md:pt-40 md:pb-28 overflow-hidden bg-hero-gradient">
        
        {{-- Efek Glowing Radial di Background --}}
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-teal-400/20 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-orange-400/10 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 md:px-6 grid md:grid-cols-2 gap-12 items-center relative z-10">
            
            {{-- Kiri: Teks --}}
            <div data-aos="fade-up" data-aos-duration="1000">
                {{-- Badge Tagline --}}
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/60 backdrop-blur-sm border border-teal-100 shadow-sm mb-6">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                    </span>
                    <span class="text-[10px] md:text-xs font-bold text-teal-700 tracking-wide uppercase">Sistem Restoran Modern & QR Code</span>
                </div>

                {{-- Headline Gradasi --}}
                <h1 class="text-3xl md:text-5xl lg:text-6xl font-black leading-[1.15] text-gray-900 mb-6 tracking-tight">
                    Pelanggan Ramai <br> tapi Antrean Pesanan 
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-500 to-emerald-500">
                        Berantakan?
                    </span>
                </h1>
                
                <p class="text-gray-600 text-sm md:text-lg mb-8 max-w-md leading-relaxed">
                    Digitalisasi sistem pesanan restoran & kafe tanpa ribet. Kelola pesanan, meja, dan pembayaran dalam satu sistem yang mudah digunakan.
                </p>

                {{-- CTA Buttons (Menggunakan wire:navigate) --}}
                <div class="flex flex-col lg:flex-row gap-4 mb-12">
                    <a href="{{ route('register') }}" wire:navigate class="group relative bg-teal-500 hover:bg-teal-600 text-white text-center font-bold px-6 md:px-8 py-3.5 rounded-full shadow-xl shadow-teal-500/30 transition-all active:scale-95 flex items-center justify-center gap-2 overflow-hidden w-full lg:w-auto">
                        <span class="relative z-10 whitespace-nowrap">Daftar Restoran Sekarang</span>
                        <svg class="w-4 h-4 relative z-10 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                    <button type="button" onclick="openDemoModal()" class="bg-white border-2 border-gray-200 text-gray-700 text-center font-bold px-6 md:px-8 py-3.5 rounded-full hover:border-teal-500 hover:text-teal-600 transition-all flex items-center justify-center gap-2 w-full lg:w-auto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="whitespace-nowrap">Lihat Demo Dashboard</span>
                    </button>
                </div>

                {{-- Stats Counter (Responsive) --}}
                <div class="grid grid-cols-2 md:flex md:items-center md:gap-8 text-sm border-t border-gray-200/60 pt-6 gap-4">
                    
                    <div>
                        <p class="text-xl md:text-3xl font-black text-gray-900">500+</p>
                        <p class="text-gray-500 font-medium text-[10px] md:text-sm leading-tight">Restoran Bergabung</p>
                    </div>
                    
                    {{-- Sembunyikan garis pemisah di HP --}}
                    <div class="hidden md:block w-px h-10 bg-gray-300"></div>
                    
                    <div>
                        <p class="text-xl md:text-3xl font-black text-gray-900">11k+</p>
                        <p class="text-gray-500 font-medium text-[10px] md:text-sm leading-tight">Pesanan Diproses</p>
                    </div>
                    
                    {{-- Sembunyikan garis pemisah di HP --}}
                    <div class="hidden md:block w-px h-10 bg-gray-300"></div>
                    
                    {{-- Di HP, rating turun ke bawah dan mengambil 2 kolom penuh --}}
                    <div class="col-span-2 md:col-span-1 flex md:block items-center md:items-start gap-2">
                        <p class="text-xl md:text-3xl font-black text-orange-500">4.9★</p>
                        <p class="text-gray-500 font-medium text-[10px] md:text-sm leading-tight">Rating Pengguna</p>
                    </div>
                </div>
            </div>

            {{-- Kanan: MOCKUP DASHBOARD (Floating Animation + QR Asli) --}}
            <div class="relative hidden md:block" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                {{-- Container Animasi Melayang --}}
                <div class="relative animate-[float_6s_ease-in-out_infinite]">
                    
                    {{-- Kartu Utama --}}
                    <div class="bg-white p-2 rounded-3xl shadow-2xl shadow-teal-900/10 border border-white/50 backdrop-blur-xl hover:scale-[1.02] transition-transform duration-500">
                        <div class="bg-gray-50/80 rounded-[20px] h-[360px] overflow-hidden flex flex-col relative">
                            {{-- Top Bar --}}
                            <div class="bg-white/80 px-4 py-3 border-b border-gray-100 flex items-center justify-between backdrop-blur-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-teal-500 rounded-md flex items-center justify-center text-white text-xs font-bold">K</div>
                                    <span class="text-xs font-bold text-gray-800">Kopling Resto</span>
                                </div>
                                <div class="flex gap-1.5">
                                    <div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-yellow-400"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-green-400"></div>
                                </div>
                            </div>
                            
                            {{-- Body --}}
                            <div class="flex-1 p-4 grid grid-cols-3 gap-3">
                                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 space-y-3 hidden sm:block">
                                    <div class="h-2 bg-gray-200 rounded w-3/4"></div>
                                    <div class="h-2 bg-gray-200 rounded w-1/2"></div>
                                    <div class="h-2 bg-gray-200 rounded w-5/6"></div>
                                    <div class="h-2 bg-teal-200 rounded w-full mt-4"></div>
                                    <div class="h-2 bg-gray-200 rounded w-2/3"></div>
                                </div>
                                <div class="col-span-3 sm:col-span-2 space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-100">
                                            <div class="text-[10px] text-gray-400 mb-1">Pesanan Hari Ini</div>
                                            <div class="font-bold text-gray-800 text-lg">124</div>
                                        </div>
                                        <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-100">
                                            <div class="text-[10px] text-gray-400 mb-1">Pendapatan</div>
                                            <div class="font-bold text-teal-600 text-lg">Rp 4.2Jt</div>
                                        </div>
                                    </div>
                                    <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-100">
                                        <div class="text-[10px] text-gray-400 mb-2">Grafik Penjualan</div>
                                        <div class="flex items-end gap-1.5 h-16">
                                            <div class="w-full bg-teal-100 rounded-t-sm" style="height: 40%"></div>
                                            <div class="w-full bg-teal-200 rounded-t-sm" style="height: 60%"></div>
                                            <div class="w-full bg-teal-300 rounded-t-sm" style="height: 50%"></div>
                                            <div class="w-full bg-teal-400 rounded-t-sm" style="height: 80%"></div>
                                            <div class="w-full bg-teal-500 rounded-t-sm" style="height: 95%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Floating Notification (Bounce) --}}
                    <div class="absolute -top-6 -right-6 bg-white px-4 py-2.5 rounded-2xl shadow-xl border border-gray-100 flex items-center gap-2 font-bold text-sm text-teal-700 animate-bounce">
                        <span class="text-lg">✅</span> Pesanan Masuk!
                    </div>

                    {{-- Floating QR Card (Kanan Bawah) - MENGGUNAKAN QR ASLI --}}
                    <div class="absolute -bottom-8 -right-8 bg-white p-4 rounded-2xl shadow-xl border border-gray-100 w-40 rotate-6 hover:rotate-0 transition-transform duration-300">
                        <div class="w-full aspect-square bg-white rounded-lg flex items-center justify-center mb-2 overflow-hidden border border-gray-100">
                            {{-- Generate QR Code Asli menggunakan library simple-qrcode --}}
                            {!! QrCode::size(150)->margin(1)->generate(url('/')) !!}
                        </div>
                        <p class="text-[10px] font-bold text-gray-800 text-center">Scan QR</p>
                        <p class="text-[9px] text-gray-500 text-center">Meja 05</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- CSS Animasi Float --}}
        <style>
            @keyframes float {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-15px); }
                100% { transform: translateY(0px); }
            }
        </style>
    </section>

    {{-- ================= FITUR UNGGULAN (Dinamis) ================= --}}
    <section id="fitur" class="py-24 bg-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-up">
                <p class="text-teal-600 font-bold text-xs tracking-widest uppercase mb-2">Fitur Canggih</p>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900">Pengaturan Tanpa Batas</h2>
                <p class="text-gray-500 mt-4 max-w-2xl mx-auto">Kustomisasi penuh sesuai kebutuhan restoran Anda. Mulai dari pembayaran, logo, hingga QR antrian.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Card 1: QRIS & Nominal Manual --}}
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 hover:border-teal-500 transition-all duration-300 group" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">QRIS & Nominal Manual</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4">Belum pakai payment gateway? Tidak masalah. Atur QRIS sendiri dan pelanggan bisa memasukkan nominal pembayaran secara manual.</p>
                    <div class="inline-block bg-gray-50 text-xs font-semibold text-gray-500 px-3 py-1 rounded-md">Fitur Pengaturan</div>
                </div>

                {{-- Card 2: Sosial Media --}}
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 hover:border-blue-500 transition-all duration-300 group" data-aos="fade-up" data-aos-delay="150">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Atur Sosial Media</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4">Hubungkan Instagram, TikTok, dan YouTube restoran Anda. Tampilkan di halaman pelanggan untuk meningkatkan *engagement*.</p>
                    <div class="inline-block bg-gray-50 text-xs font-semibold text-gray-500 px-3 py-1 rounded-md">Kustomisasi Profil</div>
                </div>

                {{-- Card 3: Logo Restoran --}}
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 hover:border-orange-500 transition-all duration-300 group" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-12 h-12 bg-orange-50 text-orange-500 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Pasang Logo Sendiri</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4">Unggah logo restoran Anda dengan mudah. Sistem akan otomatis menyesuaikan ukuran dan menampilkannya di semua halaman.</p>
                    <div class="inline-block bg-gray-50 text-xs font-semibold text-gray-500 px-3 py-1 rounded-md">Branding</div>
                </div>

                {{-- Card 4: QR Antrian --}}
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 hover:border-purple-500 transition-all duration-300 group" data-aos="fade-up" data-aos-delay="250">
                    <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">QR Code Antrian</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4">Buat QR Code unik untuk setiap meja atau antrian. Pelanggan tinggal scan, pesanan langsung masuk tanpa antre di kasir.</p>
                    <div class="inline-block bg-gray-50 text-xs font-semibold text-gray-500 px-3 py-1 rounded-md">Sistem Antrian</div>
                </div>
                
                {{-- Card 5: Laporan Excel & Struk --}}
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 hover:border-pink-500 transition-all duration-300 group" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-12 h-12 bg-pink-50 text-pink-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Laporan Excel & Struk</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4">Unduh rekapan penjualan harian/bulanan langsung ke format Excel. Serta cetak struk pesanan untuk diberikan ke pembeli.</p>
                    <div class="inline-block bg-gray-50 text-xs font-semibold text-gray-500 px-3 py-1 rounded-md">Laporan Bisnis</div>
                </div>

                {{-- Card 6: Keamanan PIN QRIS --}}
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 hover:border-indigo-500 transition-all duration-300 group" data-aos="fade-up" data-aos-delay="350">
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Keamanan PIN QRIS</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4">Lindungi gambar QRIS dan pengaturan pembayaran Anda. Hanya pemilik yang mengetahui PIN yang bisa mengubah foto QRIS dan nama pemilik.</p>
                    <div class="inline-block bg-gray-50 text-xs font-semibold text-gray-500 px-3 py-1 rounded-md">Keamanan Ekstra</div>
                </div>
            </div>
        </div>
    </section>

        {{-- ================= TESTIMONI (RANDOM + RESPONSIVE) ================= --}}
    <section id="testimoni" class="py-24 bg-teal-50/50 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 md:px-6"> {{-- Padding dikurangi di HP --}}
            <div class="text-center mb-16" data-aos="fade-up">
                <p class="text-teal-600 font-bold text-xs tracking-widest uppercase mb-2">Testimoni Pelanggan</p>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900">Dipercaya Ratusan Restoran</h2>
            </div>

            {{-- Container Testimoni --}}
            <div class="relative" data-aos="fade-up" data-aos-delay="100">
                
                {{-- Tombol Navigasi Manual (Disembunyikan di HP agar tidak menutupi kartu) --}}
                <button onclick="scrollTestimoni(-1)" class="hidden md:flex absolute left-0 top-1/2 -translate-y-1/2 -ml-4 md:-ml-6 z-20 w-10 h-10 bg-white rounded-full shadow-lg border border-gray-100 items-center justify-center text-gray-600 hover:text-teal-600 hover:scale-110 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button onclick="scrollTestimoni(1)" class="hidden md:flex absolute right-0 top-1/2 -translate-y-1/2 -mr-4 md:-mr-6 z-20 w-10 h-10 bg-white rounded-full shadow-lg border border-gray-100 items-center justify-center text-gray-600 hover:text-teal-600 hover:scale-110 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>

                {{-- Scrollable Area (Lebar kartu disesuaikan) --}}
                <div id="testimoni-container" class="flex overflow-x-auto gap-4 md:gap-6 pb-8 snap-x snap-mandatory scroll-smooth custom-scrollbar-hide">
                    @php
                        $nama = [
                            'Siti Nurhaliza', 'Budi Santoso', 'Andi Pratama', 'Dewi Lestari', 'Rizky Pratama', 
                            'Maya Sari', 'Fajar Nugroho', 'Lina Marlina', 'Hendra Wijaya', 'Rina Oktaviani', 
                            'Agus Setiawan', 'Tika Wulandari', 'Bambang Pamungkas', 'Nia Ramadhani', 'Reza Rahadian',
                            'Raisa Andriana', 'Iqbaal Ramadhan', 'Chelsea Olivia', 'Baim Wong', 'Nagita Slavina'
                        ];
                        
                        $resto = [
                            'Resto Kampung Daun', 'Warung Sari Rasa', 'Supli Go', 'Kafe Kopi Senja', 'Bakso Pak Kumis', 
                            'Sate Taichan Goreng', 'Nasi Goreng Gila', 'Ayam Geprek Bensu', 'Mie Ayam Pak Kumis', 'Es Teh Indonesia', 
                            'Martabak Bangka', 'Soto Betawi H. Mamat', 'Pecel Lele Lela', 'Bebek Kaleyo', 'Warung Nasi Padang',
                            'Kedai Kopi Kenangan', 'Ayam Bakar Mas Mono', 'Sushi Tei', 'Pizza Hut', 'Burger King'
                        ];
                        
                        $pesan = [
                            'Fiturnya lengkap dan mudah digunakan. Laporan penjualannya juga jelas.',
                            'Sejak pakai Kopling, orderan rapi dan pelanggan puas. Support-nya responsif!',
                            'Dulu antrean berantakan, sekarang semua teratur. Sangat membantu bisnis.',
                            'Pelanggan sekarang bisa pesan sendiri tanpa nunggu pelayan. Hemat waktu!',
                            'Laporan harian otomatis, jadi saya nggak perlu hitung manual lagi.',
                            'Fitur QR code-nya juara. Pelanggan tinggal scan, pesanan langsung masuk.',
                            'Sangat *worth it*! Omzet naik karena pelanggan nggak perlu antre lama.',
                            'Tampilan dashboard-nya modern banget, gampang dipahami karyawan baru.',
                            'Saya suka fitur QRIS-nya, pembayaran jadi lebih cepat dan aman.',
                            'Fitur cetak struknya sangat membantu operasional kasir sehari-hari.',
                            'Awalnya ragu, tapi setelah coba ternyata sangat memudahkan manajemen resto.',
                            'Harga terjangkau dengan fitur yang sangat lengkap. Recommended!',
                            'Sangat memudahkan saya memantau penjualan dari mana saja.',
                            'Pelanggan jadi lebih betah karena nggak nunggu lama.',
                            'Sistemnya stabil, jarang error. Tim IT-nya kerja bagus.',
                            'Fitur varian menu sangat membantu untuk kustomisasi pesanan.',
                            'Saya bisa lihat laporan mingguan dengan mudah, tanpa ribet.',
                            'QR Code-nya bisa dicetak sendiri, hemat biaya operasional.',
                            'Sangat cocok untuk resto yang lagi berkembang pesat.',
                            'Fitur PIN QRIS membuat saya tenang, data pembayaran aman.'
                        ];

                        shuffle($nama);
                        shuffle($resto);
                        shuffle($pesan);

                        $totalTampil = 6;
                    @endphp

                    @for ($i = 0; $i < $totalTampil; $i++)
                        @php
                            $currentNama = $nama[$i];
                            $currentResto = $resto[$i];
                            $currentPesan = $pesan[$i];
                            $initial = substr($currentNama, 0, 1);
                            $rating = rand(4, 5);
                        @endphp

                        {{-- LEBAR KARTU: 85vw di HP, 350px di Desktop --}}
                        <div class="w-[85vw] md:w-[350px] max-w-[350px] flex-shrink-0 bg-white p-6 rounded-3xl shadow-sm border border-gray-100 snap-center">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-teal-400 to-teal-600 text-white rounded-full flex items-center justify-center font-bold text-lg shadow-sm flex-shrink-0">
                                    {{ $initial }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-gray-900 text-sm truncate">{{ $currentNama }}</h4>
                                    <p class="text-xs text-gray-500 truncate">{{ $currentResto }}</p>
                                </div>
                            </div>
                            <p class="text-gray-600 text-sm italic mb-4 line-clamp-3">"{{ $currentPesan }}"</p>
                            
                            <div class="flex text-orange-400 gap-0.5">
                                @for ($star = 1; $star <= 5; $star++)
                                    @if ($star <= $rating)
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @else
                                        <svg class="w-4 h-4 fill-current text-gray-200" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </section>

    {{-- ================= CARA MULAI (ANIMASI ALUR KERJA + GARIS MENYALA) ================= --}}
    <section id="cara-mulai" class="py-24 bg-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-up">
                <p class="text-teal-600 font-bold text-xs tracking-widest uppercase mb-2">Cara Mulai</p>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900">Mulai dalam 4 Langkah Mudah</h2>
            </div>

            <div class="grid md:grid-cols-4 gap-8 relative" data-aos="fade-up" data-aos-delay="100">
                
                {{-- Garis Penghubung Background (Abu-abu) --}}
                <div class="hidden md:block absolute top-8 left-[10%] right-[10%] h-0.5 bg-gray-200 z-0"></div>

                {{-- Garis Animasi (Warna Teal) yang akan menyala mengikuti alur --}}
                <div class="hidden md:block absolute top-8 left-[10%] right-[10%] h-0.5 bg-teal-500 z-0 origin-left animate-[progress-line_8s_infinite]"></div>

                @php
                    $steps = [
                        ['ikon' => '📝', 'judul' => 'Daftar Akun', 'desc' => 'Buat akun restoran dalam 1 menit, tanpa biaya awal.'],
                        ['ikon' => '⚙️', 'judul' => 'Atur Toko & Profil', 'desc' => 'Lengkapi nama toko, logo, alamat, dan pengaturan pembayaran (QRIS).'],
                        ['ikon' => '📋', 'judul' => 'Upload Menu & Varian', 'desc' => 'Tambahkan produk, harga, dan atur apakah pembeli wajib memilih varian atau tidak.'],
                        ['ikon' => '🔲', 'judul' => 'Cetak QR Code Meja', 'desc' => 'Generate QR Code unik untuk setiap meja dan tempelkan di meja pelanggan.'],
                    ];
                @endphp

                @foreach ($steps as $i => $s)
                    <div class="relative z-10 text-center flex flex-col items-center group">
                        
                        {{-- Ikon dengan animasi alur kerja --}}
                        <div class="step-icon w-16 h-16 bg-white border-2 border-gray-200 text-gray-400 rounded-full flex items-center justify-center text-2xl mb-6 shadow-sm" 
                             style="animation-delay: {{ $i * 2 }}s;">
                            {{ $s['ikon'] }}
                        </div>
                        
                        <p class="step-label text-xs font-bold text-gray-400 mb-2 uppercase tracking-widest transition-colors duration-300"
                           style="animation-delay: {{ $i * 2 }}s;">
                           Langkah {{ $i + 1 }}
                        </p>
                        
                        <h3 class="font-bold text-lg text-gray-900 mb-2">{{ $s['judul'] }}</h3>
                        <p class="text-gray-500 text-sm max-w-xs">{{ $s['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- CSS ANIMASI --}}
        <style>
            /* Animasi Ikon */
            .step-icon {
                animation: pulse-step 8s infinite;
            }
            .step-label {
                animation: color-step 8s infinite;
            }

            /* Animasi Garis (Progress Bar) */
            @keyframes progress-line {
                0% { transform: scaleX(0); opacity: 1; }
                25% { transform: scaleX(0.33); opacity: 1; } /* Selesai Langkah 1 */
                50% { transform: scaleX(0.66); opacity: 1; } /* Selesai Langkah 2 */
                75% { transform: scaleX(1); opacity: 1; }    /* Selesai Langkah 3 */
                100% { transform: scaleX(1); opacity: 0; }   /* Fade out sebelum reset */
            }

            /* Animasi Ikon Bergantian */
            @keyframes pulse-step {
                0%, 20% {
                    border-color: #14b8a6;
                    background-color: #f0fdfa;
                    color: #0f766e;
                    transform: scale(1.1);
                    box-shadow: 0 10px 15px -3px rgba(20, 184, 166, 0.2);
                }
                25%, 100% {
                    border-color: #e5e7eb;
                    background-color: #ffffff;
                    color: #9ca3af;
                    transform: scale(1);
                    box-shadow: none;
                }
            }

            @keyframes color-step {
                0%, 20% { color: #0f766e; }
                25%, 100% { color: #9ca3af; }
            }
        </style>
    </section>
@endsection

@push('scripts')
<script>
    // 1. FITUR MODAL DEMO
    function openDemoModal() {
        const modal = document.getElementById('demo-modal');
        const content = document.getElementById('demo-modal-content');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeDemoModal() {
        const modal = document.getElementById('demo-modal');
        const content = document.getElementById('demo-modal-content');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    // 2. FITUR AUTO-SLIDE TESTIMONI & MANUAL SCROLL
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('testimoni-container');
        let scrollInterval;
        
        window.scrollTestimoni = function(direction) {
            const cardWidth = container.querySelector('.snap-center').offsetWidth + 24;
            container.scrollBy({ left: direction * cardWidth, behavior: 'smooth' });
            resetAutoSlide();
        };

        function startAutoSlide() {
            scrollInterval = setInterval(() => {
                if (container.scrollLeft + container.offsetWidth >= container.scrollWidth - 10) {
                    container.scrollTo({ left: 0, behavior: 'smooth' });
                } else {
                    const cardWidth = container.querySelector('.snap-center').offsetWidth + 24;
                    container.scrollBy({ left: cardWidth, behavior: 'smooth' });
                }
            }, 3500);
        }

        function resetAutoSlide() {
            clearInterval(scrollInterval);
            startAutoSlide();
        }

        startAutoSlide();

        container.addEventListener('mouseenter', () => clearInterval(scrollInterval));
        container.addEventListener('mouseleave', startAutoSlide);
    });
</script>

<style>
    /* Menghilangkan Scrollbar Bawaan pada Testimoni agar Lebih Bersih */
    .custom-scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .custom-scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endpush