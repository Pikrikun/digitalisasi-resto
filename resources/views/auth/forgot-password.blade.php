<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Kopling Grup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
    @livewireStyles
</head>
<body class="bg-gradient-to-br from-teal-50 to-white min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-gray-100 p-8">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-teal-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <span class="text-3xl">🔑</span>
            </div>
            <h1 class="text-2xl font-extrabold">Lupa Password?</h1>
            <p class="text-sm text-gray-500 mt-1">Masukkan email Anda. Kami akan mengirim kode OTP.</p>
        </div>

        @if (session('success'))
            <div class="bg-teal-50 text-teal-700 text-sm rounded-xl p-3 mb-4">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-50 text-red-600 text-sm rounded-xl p-3 mb-4">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold mb-2">Email Terdaftar</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    placeholder="email@restoran.com"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none text-sm">
            </div>
            <button type="submit" class="w-full bg-teal-500 hover:bg-teal-600 text-white font-bold py-3.5 rounded-xl shadow-md transition">
                Kirim Kode OTP
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Ingat password? <a href="{{ route('login') }}" class="text-teal-600 font-semibold" wire:navigate>Kembali Login</a>
        </p>
    </div>
    @livewireScripts
</body>
</html>