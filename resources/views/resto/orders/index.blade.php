@extends('layouts.app')

@section('title', 'Riwayat Pesanan - ' . $restaurant->name)

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-extrabold">Riwayat Pesanan</h2>
        <p class="text-sm text-gray-500">Pantau semua transaksi restoran Anda</p>
    </div>
    
    <div class="text-xs font-semibold text-teal-600 bg-teal-50 px-3 py-1.5 rounded-full flex items-center gap-2 self-start md:self-auto">
        <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span> Live
    </div>
</div>

{{-- INPUT PENCARIAN --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 mb-6">
    <div class="relative">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
        <input type="text" id="search-order" oninput="window.filterOrders()" 
            placeholder="Cari No. Pesanan (misal: 0011) atau Nama Pelanggan..." 
            class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none text-sm bg-gray-50/50 transition">
    </div>
</div>

{{-- FILTER TAB --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-2 mb-6 flex gap-2 overflow-x-auto flex-nowrap w-full snap-x">
    @php
        $tabs = [
            ['key' => 'semua', 'label' => 'Semua'],
            ['key' => 'berlangsung', 'label' => 'Verifikasi Tunai'],
            ['key' => 'verifikasi', 'label' => 'Verifikasi QRIS'],
            ['key' => 'done', 'label' => 'Selesai'],
            ['key' => 'cancelled', 'label' => 'Dibatalkan'],
        ];
        $currentStatus = request('status', 'semua');
    @endphp

    @foreach ($tabs as $tab)
        <a href="{{ route('resto.orders.index', ['status' => $tab['key']]) }}"
           wire:navigate
           class="px-5 py-2.5 rounded-lg text-sm font-bold whitespace-nowrap transition
                  {{ $currentStatus === $tab['key'] ? 'bg-teal-500 text-white shadow-md shadow-teal-200' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>

{{-- CONTAINER UNTUK LIST PESANAN --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="order-grid">
    @include('resto.orders._cards', ['orders' => $orders])
</div>

{{-- MODAL GLOBAL DETAIL PESANAN --}}
<div id="global-order-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto relative">
        <button type="button" onclick="document.getElementById('global-order-modal').classList.add('hidden'); document.getElementById('global-order-modal').classList.remove('flex');" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl leading-none">×</button>
        <div id="global-order-content"></div>
    </div>
</div>

{{-- MODAL ALASAN TOLAK --}}
<div id="reject-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
        <h3 class="font-bold text-lg mb-2 text-red-600">Tolak Pembayaran</h3>
        <p class="text-sm text-gray-500 mb-4">Berikan alasan mengapa bukti transfer ini tidak valid.</p>
        
        <textarea id="reject-reason" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-400 outline-none text-sm mb-4" placeholder="Contoh: Nominal tidak sesuai / Bukti tidak jelas"></textarea>
        <input type="hidden" id="reject-order-id">
        
        <div class="flex gap-2">
            <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')" class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm">Batal</button>
            <button type="button" onclick="window.submitReject()" class="flex-1 px-4 py-2.5 rounded-xl bg-red-500 text-white font-semibold text-sm shadow-md">Kirim Penolakan</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<audio id="notif-audio" src="https://actions.google.com/sounds/v1/alarms/beep_short.ogg" preload="auto"></audio>
<script>
    const currentStatus = "{{ request('status', 'semua') }}";
    let currentOrderCount = {{ $orders->count() }};

    // 1. FITUR PENCARIAN
window.filterOrders = function() {
    const query = document.getElementById('search-order').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.order-card');
    
    cards.forEach(card => {
        const searchData = card.getAttribute('data-search');
        const orderId = card.getAttribute('data-order-id');
        
        // Cari berdasarkan data-search (No. Pesanan / Nama) 
        // ATAU berdasarkan ID asli pesanan (misal: 2)
        if (searchData.includes(query) || orderId.includes(query)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
};

    // 2. FITUR BUKA MODAL DETAIL (Dari Data JSON Card)
    window.openOrderDetail = function(cardElement) {
        const order = JSON.parse(cardElement.getAttribute('data-order'));
        const content = document.getElementById('global-order-content');

                let itemsHtml = order.items.map(i => {
            let variantHtml = '';
            let noteHtml = '';

            // Cek jika variants berupa Array (bukan string JSON)
            if (i.variants && i.variants.length > 0) {
                let variantsData = i.variants;
                // Jika datanya berbentuk string (dari DB), ubah jadi Array
                if (typeof variantsData === 'string') {
                    try { variantsData = JSON.parse(variantsData); } catch(e) {}
                }
                
                if (Array.isArray(variantsData) && variantsData.length > 0) {
                    const variantText = variantsData.map(v => `${v.option}${v.price > 0 ? ' (+Rp ' + new Intl.NumberFormat('id-ID').format(v.price) + ')' : ''}`).join(', ');
                    variantHtml = `<p class="text-xs text-gray-500 ml-2 mt-1">Varian: ${variantText}</p>`;
                }
            }

            // Cek Catatan
            if (i.note && i.note !== 'null') {
                noteHtml = `<p class="text-xs text-orange-500 ml-2 mt-1 italic">Catatan: ${i.note}</p>`;
            }

            return `
                <li class="flex flex-col border-b border-gray-100 pb-2 mb-2">
                    <div class="flex justify-between text-sm">
                        <span class="font-semibold">${i.product_name} x${i.qty}</span>
                        <span class="font-bold">Rp ${new Intl.NumberFormat('id-ID').format(i.price * i.qty)}</span>
                    </div>
                    ${variantHtml}
                    ${noteHtml}
                </li>
            `;
        }).join('');

        // Rakit Tombol Cetak Struk (SELALU ADA, tapi disembunyikan jika pending)
        let printHtml = `
            <div class="print-container ${order.status !== 'pending' ? '' : 'hidden'} mt-4 mb-4">
                <a href="/resto/orders/${order.id}/print" target="_blank" class="w-full bg-gray-800 text-white font-bold py-3 rounded-xl shadow-md transition flex items-center justify-center gap-2">🖨️ Cetak Struk</a>
            </div>
        `;

        // Rakit Tombol Aksi (Verifikasi/Tolak)
        let proofHtml = '';
        if (order.payment_method === 'qris' && order.payment_proof_url) {
            proofHtml = `
                <div class="action-buttons mt-4 border-t border-gray-200 pt-4">
                    <p class="text-xs font-bold text-gray-500 uppercase mb-2">Bukti Transfer:</p>
                    <img src="${order.payment_proof_url}" onclick="window.openLightbox(this.src)" class="cursor-pointer w-full max-h-64 object-contain rounded-lg border mb-4">
                    
                    <p class="text-xs text-gray-500 text-center mb-4">Dikirim pada: ${order.updated_at} WIB</p>

                    ${order.status === 'pending' ? `
                        <div class="flex gap-2">
                            <button type="button" onclick="window.openRejectModal(${order.id})" class="flex-1 bg-red-100 text-red-600 font-bold py-2.5 rounded-lg text-sm">Tolak</button>
                            <button type="button" onclick="window.verifyPayment(${order.id}, 'accept')" class="flex-1 bg-teal-500 text-white font-bold py-2.5 rounded-lg text-sm">Verifikasi & Terima</button>
                        </div>
                    ` : ''}
                </div>
            `;
        }else if (order.payment_method === 'cash' && order.status === 'pending') {
            proofHtml = `
                <div class="action-buttons mt-4 border-t border-gray-200 pt-4">
                    <div class="bg-orange-50 text-orange-700 text-sm p-3 rounded-lg mb-4 font-semibold text-center">💵 Pelanggan membayar tunai di kasir.</div>
                    
                    {{-- INPUT UANG DITERIMA (OPSIONAL) --}}
                    <div class="mb-3">
                        <label class="block text-xs font-bold text-gray-600 mb-1">Uang Diterima (Opsional)</label>
                        <input type="number" id="cash-received" oninput="hitungKembalian(${order.total})" placeholder="Contoh: 150000" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-400 outline-none">
                        <p id="cash-change" class="text-sm font-bold text-teal-600 mt-2 hidden">Kembalian: Rp 0</p>
                    </div>

                    <button type="button" onclick="window.verifyPayment(${order.id}, 'accept')" class="w-full bg-teal-500 text-white font-bold py-3 rounded-lg text-sm mt-2">Konfirmasi Pembayaran Kasir</button>
                </div>
            `;
        }

                content.innerHTML = `
            <h3 class="font-bold text-lg mb-4">Detail Pesanan #ORD-${String(order.id).padStart(4, '0')}</h3>
            <div class="bg-gray-50 p-4 rounded-xl text-sm mb-4">
                <div class="flex justify-between items-center mb-2"><span class="text-gray-500">Pelanggan</span><span class="font-bold text-gray-800">${order.customer_name}</span></div>
                <div class="flex justify-between items-center mb-2"><span class="text-gray-500">Metode Bayar</span><span class="font-bold text-teal-600 uppercase">${order.payment_method}</span></div>
                <div class="flex justify-between items-center"><span class="text-gray-500">Status</span><span class="modal-status-badge font-bold ${order.status === 'done' ? 'bg-teal-100 text-teal-700' : (order.status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')} px-2 py-1 rounded-md text-[10px]">${order.status === 'pending' ? 'Menunggu Konfirmasi' : (order.status === 'done' ? 'Selesai' : 'Dibatalkan')}</span></div>
            </div>
            <div class="mb-4">
                <p class="text-xs font-bold text-gray-500 uppercase mb-2">Rincian Menu:</p>
                <ul class="space-y-2">${itemsHtml}</ul>
            </div>
            ${order.status === 'cancelled' && order.cancel_reason ? `
                <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-3 text-left">
                    <p class="text-xs font-bold text-red-700 mb-1">Alasan Pembatalan:</p>
                    <p class="text-sm text-red-600 font-semibold">${order.cancel_reason}</p>
                </div>
            ` : ''}
            <div class="flex justify-between font-extrabold text-teal-600 text-lg border-t pt-3 mb-4">
                <span>Total</span><span>Rp ${new Intl.NumberFormat('id-ID').format(order.total)}</span>
            </div>
            ${printHtml}
            ${proofHtml}
        `;

        document.getElementById('global-order-modal').classList.remove('hidden');
        document.getElementById('global-order-modal').classList.add('flex');
    };

    // 3. FITUR VERIFIKASI (TANPA RELOAD)
    window.verifyPayment = function(orderId, action, reason = null) {
        // Jika aksinya 'accept', tampilkan modal konfirmasi cantik
        if (action === 'accept') {
            window.showConfirm(
                'Konfirmasi Pembayaran', 
                'Yakin ingin MENERIMA pembayaran ini?', 
                function() {
                    // Jika user klik "Ya, Lanjutkan", jalankan prosesnya
                    prosesVerifikasi(orderId, action, reason);
                }
            );
            return; // Hentikan eksekusi di sini, tunggu user klik tombol
        }

        // Jika aksinya 'reject' (tolak), langsung proses karena sudah ada form alasan
        prosesVerifikasi(orderId, action, reason);
    };


    function prosesVerifikasi(orderId, action, reason) {
        const cashReceivedInput = document.getElementById('cash-received');
        const cashReceived = cashReceivedInput ? cashReceivedInput.value : null;

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "/resto/orders/" + orderId + "/verify", true);
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.setRequestHeader("X-CSRF-TOKEN", document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        xhr.setRequestHeader("Accept", "application/json");

        xhr.onreadystatechange = function() {
            if (this.readyState === 4) {
                if (this.status === 200) {
                    const content = document.getElementById('global-order-content');
                    
                    const statusBadge = content.querySelector('.modal-status-badge');
                    if (statusBadge) {
                        statusBadge.textContent = 'Selesai';
                        statusBadge.className = 'modal-status-badge font-bold bg-teal-100 text-teal-700 px-2 py-1 rounded-md text-[10px]';
                    }

                    const actionButtons = content.querySelector('.action-buttons');
                    if (actionButtons) actionButtons.style.display = 'none';
                    
                    const printContainer = content.querySelector('.print-container');
                    if (printContainer) printContainer.classList.remove('hidden');
                    
                } else {
                    alert('Gagal memproses. Status Code: ' + this.status);
                }
            }
        };
        xhr.send(JSON.stringify({ action: action, reason: reason, cash_received: cashReceived }));
    }

    // Fungsi hitung kembalian otomatis
    window.hitungKembalian = function(total) {
        const inputUang = document.getElementById('cash-received').value;
        const labelKembalian = document.getElementById('cash-change');
        
        if (inputUang && Number(inputUang) > 0) {
            const kembalian = Number(inputUang) - Number(total);
            labelKembalian.classList.remove('hidden');
            
            if (kembalian >= 0) {
                labelKembalian.textContent = 'Kembalian: Rp ' + new Intl.NumberFormat('id-ID').format(kembalian);
                labelKembalian.classList.remove('text-red-600');
                labelKembalian.classList.add('text-teal-600');
            } else {
                labelKembalian.textContent = 'Uang Kurang: Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(kembalian));
                labelKembalian.classList.remove('text-teal-600');
                labelKembalian.classList.add('text-red-600');
            }
        } else {
            labelKembalian.classList.add('hidden');
        }
    };

    // 4. FITUR TOLAK
    window.openRejectModal = function(orderId) {
        document.getElementById('global-order-modal').classList.add('hidden');
        document.getElementById('reject-order-id').value = orderId;
        document.getElementById('reject-reason').value = '';
        document.getElementById('reject-modal').classList.remove('hidden');
        document.getElementById('reject-modal').classList.add('flex');
    };

    window.submitReject = function() {
        const orderId = document.getElementById('reject-order-id').value;
        const reason = document.getElementById('reject-reason').value.trim();
        if (!reason) return alert('Alasan wajib diisi!');
        document.getElementById('reject-modal').classList.add('hidden');
        document.getElementById('reject-modal').classList.remove('flex');
        window.verifyPayment(orderId, 'reject', reason);
    };

    // 5. FITUR NOTIFIKASI REAL-TIME
        // Simpan ID pesanan dan waktu update terakhirnya
    let knownOrderUpdates = {};
    document.querySelectorAll('.order-card').forEach(card => {
        const data = JSON.parse(card.getAttribute('data-order'));
        knownOrderUpdates[data.id] = data.updated_at;
    });

    function checkNewOrders() {
        fetch(`{{ route('resto.orders.partial') }}?status=${currentStatus}`)
            .then(res => res.text())
            .then(html => {
                // Ganti isi grid dengan card terbaru
                document.getElementById('order-grid').innerHTML = html;
                
                // Cek apakah ada pesanan baru atau update
                const cards = document.querySelectorAll('.order-card');
                let hasNewUpdate = false;
                let hasNewOrder = false;

                cards.forEach(card => {
                    const data = JSON.parse(card.getAttribute('data-order'));
                    
                    if (!knownOrderUpdates[data.id]) {
                        // ID belum ada di memori = Pesanan Baru
                        hasNewOrder = true;
                        knownOrderUpdates[data.id] = data.updated_at;
                    } else if (knownOrderUpdates[data.id] !== data.updated_at) {
                        // ID sudah ada, tapi waktu update beda = Ada Perubahan (misal upload ulang)
                        hasNewUpdate = true;
                        knownOrderUpdates[data.id] = data.updated_at;
                    }
                });

                if (hasNewOrder || hasNewUpdate) {
                    // Mainkan suara & tampilkan banner
                    document.getElementById('notif-audio').play().catch(e => console.log('Audio diblokir'));
                    const banner = document.getElementById('new-order-banner');
                    if (banner) {
                        banner.classList.remove('hidden'); banner.classList.add('flex');
                        setTimeout(() => { banner.classList.add('hidden'); banner.classList.remove('flex'); }, 5000);
                    }
                }
            })
            .catch(err => console.error('Error:', err));
    }

    setInterval(checkNewOrders, 5000);
</script>
@endpush