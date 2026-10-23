<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Kopling Grup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .auth-bg {
            background: linear-gradient(135deg, #ccfbf1 0%, #f0fdfa 40%, #ffffff 100%);
        }
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(70px);
            opacity: 0.5;
            z-index: 0;
        }
        .float-leaf {
            position: absolute;
            animation: floaty 4s ease-in-out infinite;
        }
        .float-leaf.delay { animation-delay: 1.5s; }
        .float-leaf.delay2 { animation-delay: 2.5s; }
        @keyframes floaty {
            0%,100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(10deg); }
        }
    </style>
    @livewireStyles
</head>
<body class="auth-bg min-h-screen flex flex-col overflow-hidden text-gray-800">

    {{-- HEADER --}}
    <header class="relative z-20 flex items-center justify-between px-6 md:px-12 py-5">
        <a href="/" class="flex items-center gap-2">
            <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center shadow-lg shadow-green-200">
                <span class="text-white font-extrabold text-xl">K</span>
            </div>
            <span class="font-bold text-lg hidden md:block">Kopling Grup</span>
        </a>
        <a href="/register" wire:navigate class="text-sm font-medium text-gray-700 hover:text-green-600 transition">
            Belum punya akun? <span class="text-green-600 font-semibold">Daftar</span>
        </a>
    </header>

    {{-- MAIN --}}
    <main class="relative z-10 flex-1 flex items-center justify-center px-6 py-10">

        {{-- Dekorasi blob --}}
        <div class="blob w-72 h-72 bg-teal-300 -top-10 -left-20"></div>
        <div class="blob w-80 h-80 bg-green-200 bottom-0 right-0"></div>

        {{-- Dekorasi daun & piring --}}
        <div class="float-leaf top-16 left-10 text-4xl">🌿</div>
        <div class="float-leaf delay top-32 right-20 text-3xl">🍃</div>
        <div class="float-leaf delay2 bottom-20 left-24 text-3xl">🌿</div>
        <div class="float-leaf bottom-16 right-10 text-4xl">🍽️</div>
        <div class="float-leaf delay top-1/2 left-6 text-3xl">🥗</div>

        {{-- Panel --}}
        <div class="relative w-full max-w-4xl grid md:grid-cols-2 bg-white/80 backdrop-blur rounded-3xl shadow-2xl border border-white overflow-hidden">

            {{-- KIRI: Ilustrasi Chef --}}
            <div class="relative hidden md:flex flex-col items-center justify-center bg-gradient-to-br from-green-100 via-teal-50 to-white p-10">
                <div class="text-8xl mb-4">👨‍🍳</div>
                <div class="bg-white rounded-2xl px-5 py-3 shadow-lg flex items-center gap-2">
                    <span class="text-2xl">📱</span>
                    <span class="font-bold text-green-600">in</span>
                    <span class="text-xs text-gray-500">Kelola Restoran</span>
                </div>
                <p class="text-center text-sm text-gray-500 mt-6 leading-relaxed">
                    Kelola pesanan, menu, dan laporan restoran Anda dalam satu dashboard.
                </p>
                <div class="absolute top-6 left-6 text-2xl">🌿</div>
                <div class="absolute bottom-6 right-6 text-2xl">🍃</div>
            </div>

            {{-- KANAN: Form --}}
            <div class="p-8 md:p-10">
                <div class="text-center md:text-left mb-8">
                    <h1 class="text-2xl md:text-3xl font-extrabold mb-2">
                        Selamat Datang <span class="text-green-600">Kembali!</span>
                    </h1>
                    <p class="text-gray-500 text-sm">
                        Silakan masuk untuk mengelola restoran Anda.
                    </p>
                </div>

                {{-- Error --}}
                @if ($errors->any())
                    <div class="mb-5 bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2">Email</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">✉️</span>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                placeholder="nama@restoran.com"
                                class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none transition bg-gray-50/50">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2">Kata Sandi</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">🔒</span>
                            <input type="password" name="password" required
                                placeholder="••••••••"
                                class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none transition bg-gray-50/50">
                        </div>
                    </div>

                    {{-- Remember --}}
                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 text-gray-600">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-green-500 focus:ring-green-300">
                            Ingat saya
                        </label>
                        <a href="{{ route('password.request') }}" class="text-teal-600 font-semibold hover:underline" wire:navigate>Lupa Kata Sandi?</a>
                    </div>

                    {{-- CTA --}}
                    <button type="submit"
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-green-200 transition">
                        Masuk
                    </button>
                </form>

                <p class="text-center text-sm text-gray-500 mt-6">
                    Belum punya akun?
                    <a href="{{ route('register') }}" wire:navigate class="text-green-600 font-semibold hover:underline">Daftar di sini</a>
                </p>
            </div>
        </div>
    </main>

    <footer class="relative z-10 text-center text-xs text-gray-400 py-4">
        © {{ date('Y') }} Kopling Grup Corporation - Semua Hak Dilindungi
    </footer>
    @livewireScripts

</body>
</html>