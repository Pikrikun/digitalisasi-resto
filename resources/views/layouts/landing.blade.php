<!DOCTYPE html>
<html lang="id">
<head>
    @livewireStyles
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Digitalisasi Resto - Solusi Pesanan QR Code')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .gradient-bg {
            background: linear-gradient(135deg, #f0fdfa 0%, #ecfeff 50%, #fff7ed 100%); /* Diubah ke Teal */
        }
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.4;
            z-index: 0;
        }
    </style>
    @stack('styles')
</head>
<body class="gradient-bg text-gray-800 overflow-x-hidden" wire:navigate>

    {{-- ================= NAVBAR ================= --}}
    <nav class="relative z-20 flex items-center justify-between px-6 md:px-16 py-5">
        {{-- Logo --}}
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <div class="w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center shadow-lg shadow-teal-200">
                <span class="text-white font-extrabold text-xl">K</span>
            </div>
            <span class="font-bold text-lg hidden md:block">Kopling Grup</span>
        </a>

        {{-- Nav Links --}}
        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-700">
            <a href="#fitur" class="hover:text-teal-600 transition">Fitur</a>
            <a href="#testimoni" class="hover:text-teal-600 transition">Testimoni</a>
            <a href="#cara-mulai" class="hover:text-teal-600 transition">Cara Mulai</a>
            <a href="#kontak" class="hover:text-teal-600 transition">Kontak</a>
        </div>

        {{-- Login Button --}}
        @if (Route::has('login'))
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('resto.dashboard') }}" wire:navigate class="bg-teal-500 hover:bg-teal-600 text-white font-semibold px-6 py-2.5 rounded-full shadow-lg shadow-teal-200 transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="text-sm font-semibold text-gray-700 hover:text-teal-600 transition">Masuk</a>
                    <a href="{{ route('register') }}" wire:navigate class="bg-teal-500 hover:bg-teal-600 text-white font-semibold px-6 py-2.5 rounded-full shadow-lg shadow-teal-200 transition">
                        Daftar
                    </a>
                @endauth
            </div>
        @endif
    </nav>

    {{-- KONTEN UTAMA --}}
    @yield('content')

    {{-- ================= FOOTER ================= --}}
    <footer id="kontak" class="relative z-10 bg-gray-900 text-gray-300 px-6 md:px-16 pt-16 pb-8 mt-10">
        <div class="max-w-6xl mx-auto grid md:grid-cols-4 gap-10 mb-12">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-9 h-9 bg-teal-500 rounded-lg flex items-center justify-center">
                        <span class="text-white font-extrabold">K</span>
                    </div>
                    <span class="font-bold text-white">Kopling Grup</span>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed">
                    Solusi digital untuk restoran & kafe modern. Kelola pesanan lebih efisien.
                </p>
            </div>

            <div>
                <h4 class="font-bold text-white mb-4">Tautan Cepat</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#fitur" class="hover:text-teal-400 transition">Fitur</a></li>
                    <li><a href="#testimoni" class="hover:text-teal-400 transition">Testimoni</a></li>
                    <li><a href="#cara-mulai" class="hover:text-teal-400 transition">Cara Mulai</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-white mb-4">Support</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="https://sociabuzz.com/hhcc" target="_blank" class="hover:text-teal-400 transition">SocialBuzz</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-white mb-4">Sosial Media</h4>
                <div class="flex gap-3 mb-5">
                    <a href="https://www.youtube.com/@fikr_al" target="_blank" class="w-9 h-9 bg-gray-800 hover:bg-teal-500 rounded-lg flex items-center justify-center transition">📷</a>
                    <a href="https://www.instagram.com/_fikri_al/" target="_blank" class="w-9 h-9 bg-gray-800 hover:bg-teal-500 rounded-lg flex items-center justify-center transition">📘</a>
                </div>
                <h4 class="font-bold text-white mb-3">Informasi</h4>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center gap-2">✉️ purwafikri@gmail.com</li>
                    <li class="flex items-center gap-2">📞 +62 81477048231</li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-6 text-center text-sm text-gray-500">
            © {{ date('Y') }} Kopling Grup Corporation - Semua Hak Dilindungi
        </div>
    </footer>
    @livewireScripts
    @stack('scripts')
</body>
</html>