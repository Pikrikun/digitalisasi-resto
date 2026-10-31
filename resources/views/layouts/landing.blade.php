<!DOCTYPE html>
<html lang="id">
<head>
    @livewireStyles
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Digitalisasi Resto - Solusi Pesanan QR Code')</title>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Animasi Fade In saat di-scroll */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Efek Gradient Halus untuk Hero */
        .bg-hero-gradient {
            background: linear-gradient(135deg, #f0fdfa 0%, #ffffff 50%, #fff7ed 100%);
        }

                /* Animasi Modal Next.js Style */
        .modal-backdrop {
            transition: opacity 0.3s ease, backdrop-filter 0.3s ease;
        }
        .modal-backdrop.hidden-modal {
            opacity: 0;
            backdrop-filter: blur(0px);
            pointer-events: none;
        }
        .modal-content {
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
        }
        .modal-content.hidden-modal {
            transform: scale(0.9) translateY(20px);
            opacity: 0;
        }

        /* Animasi Masuk Elemen (Staggered) */
        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-stagger {
            animation: slideUpFade 0.5s ease-out forwards;
            opacity: 0;
        }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }

                /* Animasi Smooth Scroll untuk Navigasi */
        html {
            scroll-behavior: smooth;
            /* Memberi jarak agar judul tidak tertutup Navbar yang sticky */
            scroll-padding-top: 80px; 
        }
    </style>
    @stack('styles')
</head>
<body class="bg-white text-gray-800 overflow-x-hidden">

    {{-- ================= NAVBAR (MODERN SAAS) ================= --}}
    <header x-data="{ scrolled: false }" 
            @scroll.window="scrolled = (window.pageYOffset > 20)"
            :class="scrolled ? 'bg-white/90 backdrop-blur-lg shadow-sm border-gray-200 py-3' : 'bg-transparent border-transparent py-5'"
            class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 border-b">
        
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                <div class="w-10 h-10 bg-gradient-to-br from-teal-400 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-teal-500/30 group-hover:scale-105 group-hover:rotate-3 transition-all duration-300">
                    <span class="text-white font-black text-xl">K</span>
                </div>
                <span class="font-extrabold text-xl tracking-tight text-gray-900 hidden md:block">Kopling Grup</span>
            </a>

            {{-- Nav Links (Desktop) --}}
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-gray-600">
                <a href="#fitur" class="relative group py-2 hover:text-teal-600 transition-colors">
                    Fitur
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-teal-500 transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="#testimoni" class="relative group py-2 hover:text-teal-600 transition-colors">
                    Testimoni
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-teal-500 transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="#cara-mulai" class="relative group py-2 hover:text-teal-600 transition-colors">
                    Cara Mulai
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-teal-500 transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="#kontak" class="relative group py-2 hover:text-teal-600 transition-colors">
                    Kontak
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-teal-500 transition-all duration-300 group-hover:w-full"></span>
                </a>
            </nav>

            {{-- Login/Register Button --}}
            <div class="flex items-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('resto.dashboard') }}" class="bg-gray-900 hover:bg-black text-white font-bold px-6 py-2.5 rounded-full shadow-lg shadow-gray-900/20 transition-all active:scale-95 flex items-center gap-2">
                            <span>Dashboard</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:block text-sm font-bold text-gray-600 hover:text-teal-600 transition px-2 py-2">Masuk</a>
                        <a href="{{ route('register') }}" class="relative group bg-teal-500 text-white font-bold px-6 py-2.5 rounded-full shadow-lg shadow-teal-500/30 transition-all active:scale-95 overflow-hidden">
                            <span class="relative z-10">Daftar</span>
                            {{-- Efek Glow --}}
                            <div class="absolute inset-0 bg-gradient-to-r from-teal-400 to-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </header>

    {{-- KONTEN UTAMA --}}
    <main class="relative z-10">
        @yield('content')
    </main>

    {{-- ================= FOOTER ================= --}}
    <footer id="kontak" class="bg-gray-900 text-gray-300 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-4 gap-12 mb-12">
            <div class="md:col-span-1">
                <a href="{{ url('/') }}" class="flex items-center gap-2 mb-4">
                    <div class="w-9 h-9 bg-teal-500 rounded-lg flex items-center justify-center">
                        <span class="text-white font-extrabold text-lg">K</span>
                    </div>
                    <span class="font-bold text-white text-lg tracking-tight">Kopling Grup</span>
                </a>
                <p class="text-sm text-gray-400 leading-relaxed mb-6">
                    Solusi digital untuk restoran & kafe modern. Kelola pesanan lebih efisien.
                </p>
            </div>

            <div>
                <h4 class="font-bold text-white mb-5 text-sm uppercase tracking-wider">Tautan Cepat</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="#fitur" class="text-gray-400 hover:text-teal-400 transition-colors">Fitur</a></li>
                    <li><a href="#testimoni" class="text-gray-400 hover:text-teal-400 transition-colors">Testimoni</a></li>
                    <li><a href="#cara-mulai" class="text-gray-400 hover:text-teal-400 transition-colors">Cara Mulai</a></li>
                    <li><a href="#kontak" class="text-gray-400 hover:text-teal-400 transition-colors">Kontak</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-white mb-5 text-sm uppercase tracking-wider">Support</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="https://sociabuzz.com/hhcc" target="_blank" class="text-gray-400 hover:text-teal-400 transition-colors">Pusat Bantuan</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-teal-400 transition-colors">Syarat & Ketentuan</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-teal-400 transition-colors">Kebijakan Privasi</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-white mb-5 text-sm uppercase tracking-wider">Sosial Media</h4>
                <div class="flex gap-3 mb-6">
                    <a href="https://www.youtube.com/@fikr_al" target="_blank" class="w-10 h-10 bg-gray-800 hover:bg-teal-500 rounded-xl flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                    </a>
                    <a href="https://www.instagram.com/_fikri_al/" target="_blank" class="w-10 h-10 bg-gray-800 hover:bg-teal-500 rounded-xl flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                </div>
                
                <h4 class="font-bold text-white mb-3 text-sm uppercase tracking-wider">Informasi</h4>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        purwafikri@gmail.com
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        +62 81477048231
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-8 mt-8 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Kopling Grup Corporation - Semua Hak Dilindungi
        </div>
    </footer>

        {{-- ================= MODAL DEMO DASHBOARD (DIPINDAHKAN KE SINI) ================= --}}
    <div id="demo-modal" class="fixed inset-0 bg-black/80 backdrop-blur-md z-[9999] hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-5xl overflow-hidden relative transform transition-all scale-95 opacity-0 duration-300" id="demo-modal-content">
            <button type="button" onclick="closeDemoModal()" class="absolute top-4 right-4 bg-white hover:bg-gray-100 text-gray-800 w-10 h-10 rounded-full flex items-center justify-center text-2xl z-50 shadow-lg border border-gray-200">&times;</button>
            
            {{-- Header Modal (Mirip Browser) --}}
            <div class="bg-gray-100 px-4 py-3 border-b border-gray-200 flex items-center gap-4">
                <div class="flex gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-400"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                    <div class="w-3 h-3 rounded-full bg-green-400"></div>
                </div>
                <div class="flex-1 bg-white rounded-md px-3 py-1 text-xs text-gray-400 border border-gray-200 text-center font-mono">
                    app.koplinggrup.com/dashboard
                </div>
            </div>

            {{-- Body Modal (Simulasi Dashboard) --}}
            <div class="p-4 bg-gray-50">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex h-[400px] md:h-[500px]">
                    
                    {{-- Sidebar --}}
                    <div class="w-16 md:w-64 bg-gray-900 text-white flex flex-col shrink-0">
                        <div class="p-4 border-b border-gray-800 hidden md:flex items-center gap-2">
                            <div class="w-6 h-6 bg-teal-500 rounded flex items-center justify-center text-xs font-bold">K</div>
                            <span class="font-bold text-sm">Kopling Resto</span>
                        </div>
                        <div class="flex-1 p-2 space-y-1">
                            <div class="flex items-center gap-3 px-3 py-2 bg-teal-500 rounded-lg text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                <span class="hidden md:inline">Dashboard</span>
                            </div>
                            <div class="flex items-center gap-3 px-3 py-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg text-sm font-medium cursor-pointer transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                <span class="hidden md:inline">Pesanan</span>
                            </div>
                            <div class="flex items-center gap-3 px-3 py-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg text-sm font-medium cursor-pointer transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                <span class="hidden md:inline">Menu</span>
                            </div>
                        </div>
                    </div>

                    {{-- Konten Dashboard --}}
                    <div class="flex-1 p-4 md:p-6 overflow-y-auto bg-gray-50">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold text-lg md:text-xl text-gray-800">Dashboard</h3>
                            <div class="w-8 h-8 bg-gray-300 rounded-full"></div>
                        </div>
                        
                        {{-- Kartu Statistik --}}
                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4 mb-6">
                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                <p class="text-xs text-gray-500 mb-1">Pendapatan Hari Ini</p>
                                <p class="font-bold text-gray-800 text-lg md:text-2xl">Rp 4.250.000</p>
                                <div class="mt-2 text-[10px] text-green-600 bg-green-50 inline-block px-2 py-1 rounded">+12% dari kemarin</div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                <p class="text-xs text-gray-500 mb-1">Total Pesanan</p>
                                <p class="font-bold text-gray-800 text-lg md:text-2xl">124</p>
                                <div class="mt-2 text-[10px] text-gray-500 bg-gray-100 inline-block px-2 py-1 rounded">Selesai 120</div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm col-span-2 lg:col-span-1">
                                <p class="text-xs text-gray-500 mb-1">Meja Terisi</p>
                                <p class="font-bold text-gray-800 text-lg md:text-2xl">8 / 12</p>
                                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                                    <div class="bg-teal-500 h-1.5 rounded-full" style="width: 66%"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Tabel Pesanan Terbaru --}}
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                <p class="font-bold text-sm text-gray-800">Pesanan Terbaru</p>
                                <span class="text-xs text-teal-600 font-bold cursor-pointer">Lihat Semua</span>
                            </div>
                            <div class="divide-y divide-gray-100">
                                <div class="flex justify-between items-center p-4 text-sm hover:bg-gray-50 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-xs">01</div>
                                        <div>
                                            <p class="font-bold text-gray-800">Meja 3</p>
                                            <p class="text-[10px] text-gray-500">2 menit lalu</p>
                                        </div>
                                    </div>
                                    <span class="text-teal-600 bg-teal-50 px-2.5 py-1 rounded-md text-xs font-bold">Selesai</span>
                                </div>
                                <div class="flex justify-between items-center p-4 text-sm hover:bg-gray-50 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-xs">02</div>
                                        <div>
                                            <p class="font-bold text-gray-800">Meja 5</p>
                                            <p class="text-[10px] text-gray-500">5 menit lalu</p>
                                        </div>
                                    </div>
                                    <span class="text-orange-500 bg-orange-50 px-2.5 py-1 rounded-md text-xs font-bold">Diproses</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Modal --}}
            <div class="p-6 bg-white border-t border-gray-200 text-center">
                <p class="text-sm text-gray-500 mb-3">Suka dengan tampilannya?</p>
                <a href="{{ route('register') }}"  class="bg-teal-500 hover:bg-teal-600 text-white font-bold px-8 py-3 rounded-full shadow-lg shadow-teal-500/30 transition-all inline-block">
                    Coba Sekarang (Gratis)
                </a>
            </div>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
    
    {{-- SCRIPT UNTUK ANIMASI SCROLL --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    
                    // Jika yang diklik adalah menu "Kontak" (href="#kontak")
                    if (targetId === '#') return;
                    
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        // Hitung posisi, kurangi 80px untuk navbar
                        const offsetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - 80;
                        
                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                        
                        // Opsional: Hapus tanda # di URL setelah selesai scroll
                        // history.pushState(null, null, targetId);
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const reveals = document.querySelectorAll('.reveal');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Tambahkan class 'active' saat masuk layar
                        entry.target.classList.add('active');
                    } else {
                        // HAPUS class 'active' saat keluar layar, agar bisa animasi lagi
                        entry.target.classList.remove('active');
                    }
                });
            }, {
                threshold: 0.1, // Muncul saat 10% elemen terlihat
                rootMargin: "0px 0px -50px 0px" 
            });

            reveals.forEach(reveal => {
                observer.observe(reveal);
            });
        });

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
    </script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: false, 
            mirror: true,
            duration: 800,
        });
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>