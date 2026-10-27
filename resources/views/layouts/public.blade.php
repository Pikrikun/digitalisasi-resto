<!DOCTYPE html>
<html lang="id">
<head>
    @livewireStyles
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Digitalisasi Resto')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; -webkit-tap-highlight-color: transparent; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Animasi khusus HP (Mobile) */
        @keyframes slideUp { 
            from { transform: translateY(100%); opacity: 0; } 
            to { transform: translateY(0); opacity: 1; } 
        }

        /* Hanya jalankan animasi di layar kecil */
        @media (max-width: 767px) {
            .slide-up { animation: slideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1); }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">

    {{-- KONTEN DINAMIS (Menu / Track) --}}
    @yield('content')

    {{-- FOOTER GLOBAL PELANGGAN --}}
    {{-- Hanya tampil jika BUKAN di halaman track --}}
    @if (isset($restaurant) && !request()->is('track/*'))
                <footer class="-mt-4 mb-10 px-4 md:px-8 max-w-6xl mx-auto relative z-10">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                
                <div class="w-14 h-14 mx-auto bg-teal-50 rounded-full flex items-center justify-center text-2xl mb-3 shadow-inner">
                    🏪
                </div>
                
                <h3 class="font-extrabold text-lg text-gray-800 mb-1">{{ $restaurant->name }}</h3>
                
                @if ($restaurant->address)
                    <p class="text-sm text-gray-500 mb-2">{{ $restaurant->address }}</p>
                @endif
                
                @if ($restaurant->phone)
                    <p class="text-sm text-gray-500 mb-4">📞 {{ $restaurant->phone }}</p>
                @endif

                                {{-- SOSIAL MEDIA --}}
                <div class="flex items-center justify-center gap-3 mb-5 mt-4">
                    
                    {{-- Instagram --}}
                    @if($restaurant->instagram_link)
                    <a href="https://instagram.com/{{ str_replace('@', '', $restaurant->instagram_link) }}" target="_blank" class="w-10 h-10 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center hover:bg-teal-500 hover:text-white transition-all duration-300 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                    </a>
                    @endif

                    {{-- WhatsApp --}}
                    @if($restaurant->whatsapp_link)
                    @php
                        $waNumber = $restaurant->whatsapp_link;
                        if (str_starts_with($waNumber, '0')) {
                            $waNumber = '62' . substr($waNumber, 1);
                        }
                    @endphp
                    <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="w-10 h-10 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center hover:bg-teal-500 hover:text-white transition-all duration-300 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                    </a>
                    @endif

                    {{-- TikTok --}}
                    @if($restaurant->tiktok_link)
                    <a href="https://tiktok.com/@{{ str_replace('@', '', $restaurant->tiktok_link) }}" target="_blank" class="w-10 h-10 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center hover:bg-teal-500 hover:text-white transition-all duration-300 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                        </svg>
                    </a>
                    @endif

                </div>

                <div class="border-t border-gray-100 pt-4">
                    <p class="text-[10px] text-gray-400 font-medium">© {{ date('Y') }} {{ $restaurant->name }}. Powered by Kopling Grup Corporation.</p>
                </div>
            </div>
        </footer>

        {{-- CONTAINER TOAST --}}
        <div id="toast-container" class="fixed top-4 left-1/2 -translate-x-1/2 z-[9999] flex flex-col gap-2 pointer-events-none w-full max-w-sm px-4"></div>

    @endif

    <script>
        // Script global jika ada (misal fungsi format Rp)
        function formatRp(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }
    </script>

    
<script>
    function showToast(message, type = 'error') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        
        let bgColor = 'bg-white';
        let icon = '⚠️';
        let borderClass = 'border-orange-500';

        if (type === 'success') {
            icon = '✅';
            borderClass = 'border-teal-500';
        } else if (type === 'error') {
            icon = '❌';
            borderClass = 'border-red-500';
        }

        toast.className = `${bgColor} text-gray-800 border-l-4 ${borderClass} shadow-2xl rounded-r-xl p-4 flex items-start gap-3 pointer-events-auto transform transition-all duration-300 -translate-y-10 opacity-0`;
        
        toast.innerHTML = `
            <span class="text-xl">${icon}</span>
            <div class="flex-1 text-sm font-semibold pt-0.5">${message}</div>
            <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 text-lg leading-none">&times;</button>
        `;

        container.appendChild(toast);

        // Animasi Masuk
        setTimeout(() => {
            toast.classList.remove('-translate-y-10', 'opacity-0');
        }, 10);

        // Hilang otomatis setelah 3 detik
        setTimeout(() => {
            toast.classList.add('-translate-y-10', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
</script>
    @stack('scripts')
    @livewireScripts
</body>
</html>