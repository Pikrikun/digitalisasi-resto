<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - Digitalisasi Resto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-teal-50 to-white min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-gray-100 p-8">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-teal-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <span class="text-3xl">📩</span>
            </div>
            <h1 class="text-2xl font-extrabold">Verifikasi OTP</h1>
            <p class="text-sm text-gray-500 mt-1">Masukkan 6 digit kode yang dikirim ke email Anda. <span class="text-orange-500 font-semibold">Jika tidak ada di Inbox, cek folder Spam/Junk.</span></p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 text-sm rounded-xl p-3 mb-4">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.reset') }}" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-semibold mb-2">Kode OTP</label>
                <input type="text" name="otp" required maxlength="6" placeholder="123456"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 outline-none text-center text-2xl tracking-widest font-bold">
            </div>

            <hr class="my-4">

            <div>
                <label class="block text-sm font-semibold mb-2">Password Baru</label>
                <input type="password" name="password" required placeholder="Min. 6 karakter"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 outline-none text-sm">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required placeholder="Ulangi password baru"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 outline-none text-sm">
            </div>

            <button type="submit" class="w-full bg-teal-500 hover:bg-teal-600 text-white font-bold py-3.5 rounded-xl shadow-md transition">
                Reset Password
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            <a href="{{ route('password.request') }}" class="text-teal-600 font-semibold">Kirim Ulang Kode</a>
        </p>
    </div>

</body>
</html>