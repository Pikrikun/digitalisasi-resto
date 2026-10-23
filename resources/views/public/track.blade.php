@extends('layouts.public')

@section('title', 'Lacak Pesanan - ' . $order->restaurant->name)

@section('content')
{{-- CONTAINER TOAST --}}
<div id="toast-container" class="fixed top-4 left-1/2 -translate-x-1/2 z-[9999] flex flex-col gap-2 pointer-events-none w-full max-w-sm px-4"></div>
<div class="p-4 md:p-8">
    <div class="max-w-md mx-auto">
        {{-- Tombol Kembali --}}
        <a href="{{ route('public.menu', ['slug' => $order->restaurant->slug, 'token' => $order->table->token]) }}" 
        class="text-sm text-teal-600 hover:underline mb-4 inline-block" wire:navigate>
            ← Kembali ke Menu
        </a>

        {{-- Notifikasi Sukses --}}
        @if (session('success'))
            <div class="mb-5 bg-teal-50 border border-teal-200 text-teal-700 text-sm rounded-xl px-4 py-3">
                ✅ {{ session('success') }}
            </div>
        @endif

        {{-- ================= AREA YANG DI-SCREENSHOT ================= --}}
        <div id="print-area" class="bg-gray-50 rounded-2xl p-1">
            
            {{-- STATUS PESANAN --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-2 text-center">
                @php
                    $statusColor = match($order->status) {
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'process' => 'bg-blue-100 text-blue-700',
                        'done' => 'bg-teal-100 text-teal-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-700',
                    };
                    $statusLabel = match($order->status) {
                        'pending' => 'Menunggu Konfirmasi Kasir',
                        'process' => 'Sedang Diproses Dapur',
                        'done' => 'Pesanan Selesai',
                        'cancelled' => 'Pembayaran Ditolak / Dibatalkan',
                        default => 'Unknown',
                    };
                @endphp

                <div class="text-6xl mb-3">
                    @if($order->status === 'cancelled') ❌
                    @elseif($order->status === 'done') 🎉
                    @else ⏳
                    @endif
                </div>
                
                <h2 class="font-extrabold text-xl mb-1">#ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</h2>
                <span class="inline-block px-3 py-1 rounded-lg text-sm font-bold {{ $statusColor }}">{{ $statusLabel }}</span>
                
                @if ($order->status === 'pending' && $order->payment_method === 'cash')
                    <div class="mt-4 bg-orange-50 border border-orange-200 rounded-xl p-3 text-left">
                        <p class="text-xs font-bold text-orange-700 mb-1">💵 Instruksi Pembayaran:</p>
                        <p class="text-xs text-orange-600">Silakan segera menuju kasir untuk melakukan pembayaran tunai sebesar <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong>.</p>
                    </div>
                @endif

                @if ($order->status === 'pending' && $order->expires_at)
                    <div class="mt-4 bg-red-50 border border-red-200 rounded-xl p-3">
                        <p class="text-xs text-red-600 mb-1 font-semibold">⏳ Sisa Waktu Pembayaran:</p>
                        <p class="text-xl font-extrabold text-red-600" id="countdown-timer">--:--</p>
                        <p class="text-[10px] text-red-500 mt-1">Pesanan akan otomatis dibatalkan jika melewati batas waktu.</p>
                    </div>
                @endif

                @if($order->status === 'cancelled')
                    <div class="mt-4 bg-red-50 border border-red-200 rounded-xl p-4 text-left">
                        <p class="text-xs font-bold text-red-700 mb-1">Alasan Penolakan:</p>
                        <p class="text-sm text-red-600 font-semibold">{{ $order->cancel_reason ?? 'Bukti pembayaran tidak valid.' }}</p>
                    </div>
                @endif
            </div>

            {{-- RINCIAN PESANAN --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-2">
                <h3 class="font-bold text-sm mb-3">Rincian Pesanan</h3>
                <ul class="space-y-2 mb-4">
                    @foreach ($order->items as $item)
                        <li class="flex flex-col border-b border-gray-100 pb-2 mb-2">
                            <div class="flex justify-between text-sm">
                                <span class="font-semibold">{{ $item->product_name }} x{{ $item->qty }}</span>
                                <span class="font-bold">Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}</span>
                            </div>
                            
                            @if ($item->variants)
                                <p class="text-xs text-gray-500 mt-1">
                                    Varian: 
                                    @if(is_array($item->variants))
                                        {{ collect($item->variants)->pluck('option')->join(', ') }}
                                    @else
                                        {{ $item->variants }}
                                    @endif
                                </p>
                            @endif

                            @if ($item->note)
                                <p class="text-xs text-orange-500 mt-1 italic">
                                    📝 {{ $item->note }}
                                </p>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <div class="flex justify-between font-extrabold text-teal-600 border-t pt-2 mb-2">
                    <span>Total</span>
                    <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-xs text-gray-500 border-t border-dashed pt-2">
                    <span>Metode Pembayaran</span>
                    <span class="font-bold uppercase text-teal-600">{{ $order->payment_method }}</span>
                </div>
                <div class="flex justify-between text-xs text-gray-500 border-t border-dashed pt-2 mt-2">
                    <span>Waktu Kirim Bukti</span>
                    <span class="font-bold text-gray-700">
                        @if ($order->payment_proof)
                            {{ $order->updated_at->format('d M Y, H:i') }} WIB
                        @else
                            Pembayaran di kasir
                        @endif
                    </span>
                </div>
            </div>
        </div>
        {{-- ================= AKHIR AREA SCREENSHOT ================= --}}

        {{-- Tombol Download (DI LUAR PRINT AREA) --}}
        <div class="grid grid-cols-2 gap-2 mb-4">
            <button type="button" onclick="downloadAsImage()" class="bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs px-3 py-2.5 rounded-lg border-2 border-gray-200 transition flex items-center justify-center gap-2 shadow-sm">
                ⬇️ Download PNG
            </button>
            <button type="button" onclick="window.print()" class="bg-teal-50 hover:bg-teal-100 text-teal-700 font-semibold text-xs px-3 py-2.5 rounded-lg border-2 border-teal-200 transition flex items-center justify-center gap-2 shadow-sm">
                🖨️ Download PDF
            </button>
        </div>

        {{-- UPLOAD BUKTI (Jika belum lunas atau ditolak) --}}
        @if ($order->payment_method === 'qris' && in_array($order->status, ['pending']))
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-bold text-sm mb-3">
                    {{ $order->status === 'cancelled' ? 'Upload Ulang Bukti Transfer' : 'Ubah Bukti Transfer' }}
                </h3>
                
                <input type="hidden" name="payment_proof" id="proof-data">
                
                <label for="proof-input" class="block border-2 border-dashed border-gray-300 rounded-xl p-4 text-center cursor-pointer hover:border-teal-400 transition mb-3">
                    <div id="proof-preview">
                        <div class="text-3xl mb-1">📷</div>
                        <p class="text-xs font-semibold">Klik untuk pilih gambar</p>
                    </div>
                </label>
                <input type="file" id="proof-input" accept="image/*" class="hidden" onchange="previewProof(event)">
                
                <button type="button" onclick="submitReupload()" id="btn-reupload" class="w-full bg-teal-500 text-white font-bold py-3 rounded-xl shadow-md transition">
                    Kirim Bukti
                </button>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<!-- Panggil library html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    // 1. FUNGSI DOWNLOAD PNG
    function downloadAsImage() {
        const element = document.getElementById('print-area');
        
        if (typeof html2canvas === 'undefined') {
            alert('Library gambar belum termuat. Coba refresh halaman.');
            return;
        }

        html2canvas(element, {
            scale: 2,
            backgroundColor: '#ffffff',
            useCORS: true 
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'Struk-ORD-{{ str_pad($order->id, 4, "0", STR_PAD_LEFT) }}.png';
            link.href = canvas.toDataURL('image/png');
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }).catch(err => {
            console.error(err);
            alert('Gagal membuat gambar. Coba lagi.');
        });
    }

    // 2. FUNGSI TIMER
    @if ($order->status === 'pending' && $order->expires_at)
        const expiresAt = new Date("{{ $order->expires_at->toIso8601String() }}").getTime();
        
        const timer = setInterval(function() {
            const now = new Date().getTime();
            const distance = expiresAt - now;

            if (distance < 0) {
                clearInterval(timer);
                document.getElementById("countdown-timer").innerHTML = "WAKTU HABIS";
                setTimeout(() => location.reload(), 2000);
                return;
            }

            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("countdown-timer").innerHTML = 
                String(minutes).padStart(2, '0') + ":" + String(seconds).padStart(2, '0');
        }, 1000);
    @endif

        // Fungsi Notifikasi Cantik (Toast)
    function showPublicToast(message, type = 'error') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        
        // Warna dan ikon berdasarkan tipe
        let borderClass = type === 'success' ? 'border-teal-500' : 'border-red-500';
        let icon = type === 'success' ? '✅' : '❌';

        toast.className = `bg-white text-gray-800 border-l-4 ${borderClass} shadow-2xl rounded-r-xl p-4 flex items-start gap-3 pointer-events-auto transform transition-all duration-300 -translate-y-10 opacity-0`;
        
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

    // Fungsi Preview yang sudah diperbaiki
    function previewProof(event) {
        const file = event.target.files[0];
        if (!file) return;

        // 1. Validasi Tipe File
        if (!file.type.startsWith('image/')) {
            showPublicToast('File harus berupa gambar (JPG, PNG, dll).', 'error');
            event.target.value = ''; // Reset input
            return;
        }

        // 2. Validasi Ukuran File
        if (file.size > 5 * 1024 * 1024) {
            showPublicToast('Ukuran file maksimal 5MB.', 'error');
            event.target.value = ''; // Reset input
            return;
        }

        // 3. Tampilkan Preview
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('proof-preview').innerHTML = `<img src="${e.target.result}" class="w-full max-h-48 object-contain rounded-lg">`;
            document.getElementById('proof-data').value = e.target.result;
            
            showPublicToast('Bukti transfer berhasil dipasang.', 'success');
        };
        reader.readAsDataURL(file);
    }

    // 4. FUNGSI POLLING STATUS
    @if ($order->status === 'pending')
        let currentStatus = "{{ $order->status }}";
        
        setInterval(function() {
            fetch(`/api/check-order-status/{{ $order->id }}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status !== currentStatus) {
                    window.location.reload();
                }
            })
            .catch(err => console.log('Cek status gagal:', err));
        }, 5000);
    @endif
    
        // 5. FUNGSI KIRIM ULANG BUKTI (SUBMIT REUPLOAD)
    function submitReupload() {
        const proofData = document.getElementById('proof-data').value;
        const btn = document.getElementById('btn-reupload');
        
        if (!proofData) {
            showPublicToast('Pilih gambar bukti transfer terlebih dahulu.', 'error');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Mengirim...';

        fetch('{{ route('public.reupload', $order->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ payment_proof: proofData })
        })
        .then(res => {
            if (res.ok) {
                showPublicToast('Bukti transfer berhasil dikirim!', 'success');
                // Reload halaman setelah 1 detik agar notifikasi terlihat
                setTimeout(() => window.location.reload(), 1000); 
            } else {
                showPublicToast('Gagal mengirim bukti. Coba lagi.', 'error');
                btn.disabled = false;
                btn.textContent = 'Kirim Bukti';
            }
        })
        .catch(() => {
            showPublicToast('Terjadi kesalahan jaringan.', 'error');
            btn.disabled = false;
            btn.textContent = 'Kirim Bukti';
        });
    }

</script>
@endpush