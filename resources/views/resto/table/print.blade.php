<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak QR - {{ $table->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">

    {{-- Tombol aksi (tidak ikut ke-print) --}}
    <div class="no-print fixed top-6 right-6 flex gap-2">
        <button onclick="window.print()"
            class="px-5 py-3 bg-teal-500 hover:bg-teal-600 text-white font-semibold rounded-xl shadow-md">
            🖨️ Print / Save as PDF
        </button>
        <button onclick="window.close()"
            class="px-5 py-3 bg-white border-2 border-gray-200 text-gray-600 font-semibold rounded-xl">
            Tutup
        </button>
    </div>

    {{-- Standee --}}
    <div class="bg-white rounded-3xl shadow-2xl p-12 w-[500px] text-center">
        <p class="text-sm font-bold text-teal-500 tracking-wider mb-1">DIGITALISASI RESTO</p>
        <h1 class="text-2xl font-extrabold text-gray-800 mb-8">{{ $restaurant->name }}</h1>

        <div class="inline-block bg-teal-50 border-4 border-teal-500 rounded-3xl p-6 mb-6">
            {!! QrCode::format('svg')->size(280)->margin(1)->generate($url) !!}
        </div>

        <h2 class="text-3xl font-extrabold text-gray-800 mb-2">{{ $table->name }}</h2>
        <p class="text-lg font-bold text-teal-500 mb-2">Scan untuk Pesan Menu</p>
        <p class="text-xs text-gray-500">
            Buka Google Lens → arahkan ke QR Code → pesan langsung dari meja Anda
        </p>

        <p class="text-[10px] text-gray-300 mt-8 break-all">{{ $url }}</p>
    </div>

</body>
</html>