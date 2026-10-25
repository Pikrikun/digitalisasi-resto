<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard - Digitalisasi Resto')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    
    @stack('styles')
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .upload-box.dragover { border-color: #14b8a6; background-color: #f0fdfa; }
        .toggle-checkbox:checked + .toggle-label { background-color: #14b8a6; }
        .toggle-checkbox:checked + .toggle-label .toggle-dot { transform: translateX(20px); }
    </style>
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">


    {{-- CONTAINER TOAST (GLOBAL) --}}
    <div id="toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none"></div>
    <audio id="notif-audio" src="https://actions.google.com/sounds/v1/alarms/beep_short.ogg" preload="auto"></audio>

    {{-- MODAL ALASAN TOLAK (GLOBAL) --}}
    <div id="reject-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
            <h3 class="font-bold text-lg mb-2 text-red-600">Tolak Pembayaran</h3>
            <p class="text-sm text-gray-500 mb-4">Berikan alasan mengapa bukti transfer ini tidak valid.</p>
            <textarea id="reject-reason" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-400 outline-none text-sm mb-4" placeholder="Contoh: Nominal tidak sesuai / Bukti tidak jelas"></textarea>
            <div class="flex gap-2">
                <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')" class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm">Batal</button>
                <button type="button" onclick="window.submitReject()" class="flex-1 px-4 py-2.5 rounded-xl bg-red-500 text-white font-semibold text-sm">Kirim Penolakan</button>
            </div>
        </div>
    </div>

    {{-- SIDEBAR & MAIN --}}
    <div class="flex min-h-screen" wire:navigate>
        {{-- SIDEBAR --}}
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden transition-opacity"></div>
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-100 flex-col transform -translate-x-full lg:translate-x-0 lg:flex transition-transform duration-300 ease-in-out flex">
            <div class="flex items-center gap-2 px-6 py-5 border-b border-gray-100">
                <div class="w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center shadow-lg shadow-teal-200">
                    <span class="text-white font-extrabold text-xl">K</span>
                </div>
                <div class="leading-tight">
                    <p class="font-bold text-sm">Digitalisasi</p>
                    <p class="text-xs text-gray-500">Resto</p>
                </div>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1">
                @php
                    $menu = [
                        ['ikon' => '🏠', 'label' => 'Dashboard', 'route' => 'resto.dashboard', 'match' => 'resto.dashboard'],
                        ['ikon' => '📋', 'label' => 'Kelola Menu', 'route' => 'resto.menu.index', 'match' => 'resto.menu.*'],
                        ['ikon' => '🔲', 'label' => 'Cetak QR Meja', 'route' => 'resto.table.index', 'match' => 'resto.table.*'],
                        ['ikon' => '🧾', 'label' => 'Riwayat Pesanan', 'route' => 'resto.orders.index', 'match' => 'resto.orders.*'],
                        ['ikon' => '⚙️', 'label' => 'Pengaturan', 'route' => 'resto.settings', 'match' => 'resto.settings'],
                    ];
                @endphp

                @foreach ($menu as $m)
                    @php $isActive = $m['match'] !== '#' && request()->routeIs($m['match']); @endphp
                    <a href="{{ $m['route'] === '#' ? '#' : route($m['route']) }}"
                       wire:navigate
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium
                              {{ $isActive ? 'bg-teal-500 text-white shadow-md shadow-teal-200' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-600' }}">
                        <span class="text-lg">{{ $m['ikon'] }}</span>
                        {{ $m['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- INFO JAM OPERASIONAL --}}
            <div class="px-4 pb-2">
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-3 flex items-center gap-3">
                    <div class="w-10 h-10 bg-teal-100 text-teal-600 rounded-lg flex items-center justify-center shrink-0">
                        🕒
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Jam Operasional</p>
                        <p class="text-xs font-bold text-gray-800">
                            {{ \Carbon\Carbon::parse(auth()->user()->restaurant->open_time)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse(auth()->user()->restaurant->close_time)->format('H:i') }}
                        </p>
                        @if(auth()->user()->restaurant->status === 'open')
                            <span class="text-[9px] bg-green-100 text-green-700 font-bold px-1.5 py-0.5 rounded mt-0.5 inline-block">Buka</span>
                        @else
                            <span class="text-[9px] bg-red-100 text-red-700 font-bold px-1.5 py-0.5 rounded mt-0.5 inline-block">Tutup</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="px-4 py-4 border-t border-gray-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 transition">
                        <span class="text-lg">🚪</span> Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- MAIN AREA --}}
        <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">
            <header class="bg-white border-b border-gray-100 px-4 md:px-8 py-4 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="lg:hidden text-2xl">☰</button>
                    <h1 class="font-bold text-lg">{{ auth()->user()->restaurant->name ?? '-' }}</h1>
                </div>

                <div class="flex items-center gap-2">
                    {{-- PERINGATAN QRIS --}}
                    @if (auth()->user()->restaurant && !auth()->user()->restaurant->qris_image)
                        <a href="{{ route('resto.settings') }}" wire:navigate class="flex items-center gap-2 bg-orange-50 text-orange-600 px-3 py-1.5 rounded-lg text-xs font-bold border border-orange-200 hover:bg-orange-100 transition animate-pulse">
                            <span>⚠️</span> Atur QRIS Anda!
                        </a>
                    @endif

                    @if (auth()->user()->restaurant && auth()->user()->restaurant->logo && file_exists(storage_path('app/public/' . auth()->user()->restaurant->logo)))
                        <img src="{{ asset('storage/' . auth()->user()->restaurant->logo) }}" class="w-10 h-10 rounded-xl object-cover border border-gray-200 shadow-sm">
                    @else
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="hidden md:block leading-tight">
                        <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">Pemilik</p>
                    </div>
                </div>
            </header>

            <main class="p-3 md:p-8 flex-1">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- TEMPAT MODAL GLOBAL --}}
    @stack('modals')

    {{-- SCRIPT --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <script src="/js/resto.js"></script>
    @livewireScripts
    @stack('scripts')

    {{-- LIGHTBOX --}}
    <div id="global-lightbox" class="fixed inset-0 bg-black/90 backdrop-blur-md z-[999] hidden items-center justify-center p-4 cursor-zoom-out" onclick="this.classList.add('hidden'); this.classList.remove('flex');">
        <img id="lightbox-img" src="" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">
    </div>

    {{-- MODAL KONFIRMASI --}}
    <div id="confirm-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
            <div class="w-16 h-16 rounded-full bg-teal-50 flex items-center justify-center mx-auto mb-4">
                <span class="text-3xl">🤔</span>
            </div>
            <h3 id="confirm-title" class="font-bold text-lg mb-2">Konfirmasi</h3>
            <p id="confirm-message" class="text-sm text-gray-500 mb-6">Apakah Anda yakin?</p>
            <div class="flex gap-2">
                <button type="button" onclick="closeConfirmModal()" class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm">Batal</button>
                <button type="button" id="confirm-btn-yes" class="flex-1 px-4 py-2.5 rounded-xl bg-teal-500 text-white font-semibold text-sm shadow-md">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>

    {{-- SCRIPT GLOBAL --}}
    <script>
        let lastOrderId = localStorage.getItem('last_order_id') || null;
        let lastUpdateAt = localStorage.getItem('last_update_at') || null;

        function checkNewOrdersGlobal() {
            fetch("{{ route('resto.orders.check') }}", { 
                headers: { 'Accept': 'application/json' } 
            })
            .then(res => res.json())
            .then(data => {
                if (data.count > 0 && data.latest_id) {
                    
                    // Cek apakah ini pesanan baru ATAU update bukti
                    const isNewOrder = data.latest_id != lastOrderId;
                    const isUpdated = data.updated_at != lastUpdateAt;

                    if (isNewOrder || isUpdated) {
                        // Simpan ID dan waktu update terbaru
                        localStorage.setItem('last_order_id', data.latest_id);
                        localStorage.setItem('last_update_at', data.updated_at);
                        lastOrderId = data.latest_id;
                        lastUpdateAt = data.updated_at;

                        // Tampilkan Notifikasi
                        let message = isNewOrder ? 'Pesanan Baru!' : 'Bukti Transfer Diperbarui!';
                        showOrderToast(data.order_number, data.payment_method, message);
                    }
                }
            })
            .catch(err => console.log('Gagal cek notifikasi:', err));
        }

        function showOrderToast(orderNumber, method, title = 'Pesanan Baru!') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `bg-white border-l-4 border-teal-500 shadow-2xl rounded-r-xl p-4 flex items-start gap-3 pointer-events-auto transform transition-all duration-300 -translate-y-10 opacity-0 min-w-[300px]`;
            
            toast.innerHTML = `
                <div class="text-2xl">🔔</div>
                <div class="flex-1">
                    <p class="font-bold text-sm text-gray-800">${title}</p>
                    <p class="text-xs text-gray-500 mt-0.5">#ORD-${String(orderNumber).padStart(4, '0')} - ${method.toUpperCase()}</p>
                </div>
                <a href="{{ route('resto.orders.index') }}" class="text-xs font-bold text-teal-600 hover:underline">Lihat</a>
                <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 text-lg leading-none">&times;</button>
            `;

            container.appendChild(toast);

            // MAIN KAN SUARA
            const audio = document.getElementById('notif-audio');
            if (audio) {
                audio.currentTime = 0;
                audio.play().catch(e => console.log('Audio diblokir browser'));
            }

            setTimeout(() => toast.classList.remove('-translate-y-10', 'opacity-0'), 10);

            setTimeout(() => {
                toast.classList.add('-translate-y-10', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        // Saat pertama load, simpan state awal tanpa bunyi
        document.addEventListener('DOMContentLoaded', () => {
            fetch("{{ route('resto.orders.check') }}")
                .then(res => res.json())
                .then(data => {
                    if (data.latest_id) {
                        localStorage.setItem('last_order_id', data.latest_id);
                        localStorage.setItem('last_update_at', data.updated_at);
                    }
                });
        });

        setInterval(checkNewOrdersGlobal, 10000);


        // 2. Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            if (sidebar.classList.contains('-translate-x-full')) {
                overlay.classList.add('hidden');
            } else {
                overlay.classList.remove('hidden');
            }
        }

        // 3. Lightbox
        window.openLightbox = function(imageSrc) {
            const lightbox = document.getElementById('global-lightbox');
            const img = document.getElementById('lightbox-img');
            img.src = imageSrc;
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
        }

        // 4. Modal Konfirmasi
        window.showConfirm = function(title, message, onConfirm) {
            document.getElementById('confirm-title').textContent = title;
            document.getElementById('confirm-message').textContent = message;
            const modal = document.getElementById('confirm-modal');
            const btnYes = document.getElementById('confirm-btn-yes');
            const newBtnYes = btnYes.cloneNode(true);
            btnYes.parentNode.replaceChild(newBtnYes, btnYes);
            newBtnYes.addEventListener('click', function() {
                closeConfirmModal();
                onConfirm();
            });
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        window.closeConfirmModal = function() {
            const modal = document.getElementById('confirm-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // 5. Toast
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            let bgColor = 'bg-white';
            let icon = '✅';
            let textColor = 'text-gray-800';
            let borderClass = 'border-teal-500';

            if (type === 'error') {
                icon = '❌';
                borderClass = 'border-red-500';
            } else if (type === 'info') {
                icon = 'ℹ️';
                borderClass = 'border-blue-500';
            }

            toast.className = `${bgColor} ${textColor} border-l-4 ${borderClass} shadow-2xl rounded-r-xl p-4 flex items-start gap-3 min-w-[300px] max-w-sm pointer-events-auto transform transition-all duration-300 translate-x-full opacity-0`;
            
            toast.innerHTML = `
                <span class="text-xl">${icon}</span>
                <div class="flex-1 text-sm font-medium pt-0.5">${message}</div>
                <button onclick="this.parentElement.remove()" class="lg:hidden text-gray-400 hover:text-gray-600 text-lg leading-none">&times;</button>
            `;

            container.appendChild(toast);
            setTimeout(() => toast.classList.remove('translate-x-full', 'opacity-0'), 10);
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }
    </script>
</body>
</html>