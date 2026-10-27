@extends('layouts.app')

@section('title', 'Dashboard - ' . $restaurant->name)

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    /* Optimasi Chart di Layar Kecil */
    #salesChart {
        max-height: 240px;
    }
</style>
@endpush

@section('content')
{{-- HEADER + FILTER (Responsive) --}}
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-xl md:text-2xl font-extrabold">Dashboard</h2>
        <p class="text-xs md:text-sm text-gray-500">Ringkasan performa restoran Anda</p>
    </div>
    
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
        {{-- FILTER TANGGAL --}}
        <form id="date-filter-form" class="flex items-center gap-2 w-full sm:w-auto">
            <input type="date" id="date-input" name="date" value="{{ $selectedDate }}" 
                class="w-full sm:w-auto px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-teal-400 outline-none shadow-sm">
        </form>

        <a href="{{ route('resto.dashboard.report') }}" class="w-full sm:w-auto justify-center bg-teal-500 hover:bg-teal-600 text-white font-bold px-4 py-2.5 rounded-xl shadow-md shadow-teal-200 transition flex items-center gap-2 text-sm">
            ⬇️ <span>Download Laporan</span>
        </a>
    </div>
</div>

{{-- KARTU STATISTIK (Responsive: 1 kolom di HP, 3 kolom di desktop) --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-5">
        <div class="flex items-center justify-between mb-2">
            <div class="w-9 h-9 md:w-10 md:h-10 bg-teal-50 rounded-xl flex items-center justify-center text-lg md:text-xl">📦</div>
            <span class="text-[10px] font-bold text-teal-600 bg-teal-50 px-2 py-1 rounded-md">{{ $selectedDate == now()->toDateString() ? 'Hari Ini' : \Carbon\Carbon::parse($selectedDate)->format('d M') }}</span>
        </div>
        <p class="text-[10px] md:text-xs text-gray-500 font-semibold mb-1">Total Pesanan</p>
        <p class="text-xl md:text-2xl font-extrabold text-gray-800"><span id="stat-total-orders">{{ $totalOrders }}</span> <span class="text-xs md:text-sm font-medium text-gray-400">Pesanan</span></p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-5">
        <div class="flex items-center justify-between mb-2">
            <div class="w-9 h-9 md:w-10 md:h-10 bg-orange-50 rounded-xl flex items-center justify-center text-lg md:text-xl">💰</div>
            <span class="text-[10px] font-bold text-orange-600 bg-orange-50 px-2 py-1 rounded-md">{{ $selectedDate == now()->toDateString() ? 'Hari Ini' : \Carbon\Carbon::parse($selectedDate)->format('d M') }}</span>
        </div>
        <p class="text-[10px] md:text-xs text-gray-500 font-semibold mb-1">Total Omzet (Selesai)</p>
        <p class="text-lg md:text-2xl font-extrabold text-gray-800 break-words">Rp <span id="stat-total-revenue">{{ number_format($totalRevenue, 0, ',', '.') }}</span></p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-5">
        <div class="flex items-center justify-between mb-2">
            <div class="w-9 h-9 md:w-10 md:h-10 bg-blue-50 rounded-xl flex items-center justify-center text-lg md:text-xl">🍽️</div>
            <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-md">Katalog</span>
        </div>
        <p class="text-[10px] md:text-xs text-gray-500 font-semibold mb-1">Total Menu Aktif</p>
        <p class="text-xl md:text-2xl font-extrabold text-gray-800">{{ $totalProducts }} <span class="text-xs md:text-sm font-medium text-gray-400">Item</span></p>
    </div>
</div>

{{-- GRAFIK PENJUALAN --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-5 mb-6">
    <h3 class="font-bold text-xs md:text-sm mb-4">Grafik Penjualan Bulan Ini ({{ date('Y') }})</h3>
    <div class="h-56 md:h-64 w-full">
        <canvas id="salesChart"></canvas>
    </div>
</div>

{{-- KANBAN PESANAN (Responsive: 1 kolom di HP, 2 kolom di desktop) --}}
<div class="mb-6">
    <h3 class="font-bold text-base md:text-lg mb-4">Papan Pesanan Real-Time</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
        {{-- KOLOM 1: PESANAN BARU --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-orange-500 rounded-full animate-pulse"></span>
                    <h4 class="font-bold text-xs md:text-sm">Pesanan Baru</h4>
                </div>
                <span class="text-[10px] md:text-xs bg-orange-50 text-orange-600 font-bold px-2 py-1 rounded-md">{{ $newOrders->count() }}</span>
            </div>

            <div class="space-y-3">
                @forelse ($newOrders as $order)
                    <div class="border border-orange-100 bg-orange-50/50 rounded-xl p-3 md:p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] md:text-xs font-bold bg-white text-orange-600 px-2 py-1 rounded-lg">#ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-[10px] md:text-xs text-gray-500">{{ $order->created_at->format('H:i') }} WIB</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center text-xs md:text-sm mb-3 gap-1">
                            <span class="font-semibold text-gray-700 truncate">{{ $order->customer_name }} ({{ $order->table->name ?? '-' }})</span>
                            <span class="font-bold text-teal-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                        <a href="{{ route('resto.orders.index') }}" wire:navigate class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold py-2.5 rounded-lg text-center transition">Lihat Pesanan</a>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 text-center py-6">Belum ada pesanan baru</p>
                @endforelse
            </div>
        </div>

        {{-- KOLOM 2: SELESAI --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-teal-500 rounded-full"></span>
                    <h4 class="font-bold text-xs md:text-sm">Selesai (Terbaru)</h4>
                </div>
                <span class="text-[10px] md:text-xs bg-teal-50 text-teal-600 font-bold px-2 py-1 rounded-md">{{ $doneOrders->count() }}</span>
            </div>

            <div class="space-y-3">
                @forelse ($doneOrders as $order)
                    <div class="border border-teal-100 bg-teal-50/50 rounded-xl p-3 md:p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] md:text-xs font-bold bg-white text-teal-600 px-2 py-1 rounded-lg">#ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-[10px] md:text-xs text-gray-500">{{ $order->created_at->format('H:i') }} WIB</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center text-xs md:text-sm gap-1">
                            <span class="font-semibold text-gray-700 truncate">{{ $order->customer_name }} ({{ $order->table->name ?? '-' }})</span>
                            <span class="font-bold text-teal-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 text-center py-6">Belum ada pesanan selesai</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function renderChart() {
        const canvas = document.getElementById('salesChart');
        if (!canvas) return;

        if (window.mySalesChart) {
            window.mySalesChart.destroy();
        }

        const ctx = canvas.getContext('2d');
        const chartData = @json($chartData);

        // Deteksi lebar layar untuk menyesuaikan tampilan chart
        const isMobile = window.innerWidth < 640;

        window.mySalesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: chartData,
                    backgroundColor: 'rgba(20, 184, 166, 0.8)',
                    borderColor: 'rgba(20, 184, 166, 1)',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            font: { size: isMobile ? 9 : 12 }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { size: isMobile ? 9 : 12 },
                            // Di HP, tampilkan singkat (1Jt, 500Rb) agar tidak kepanjangan
                            callback: function(value) { 
                                if (isMobile) {
                                    if (value >= 1000000) return (value / 1000000) + 'Jt';
                                    if (value >= 1000) return (value / 1000) + 'Rb';
                                    return value;
                                }
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', renderChart);
    document.addEventListener('livewire:navigated', renderChart);

    // Re-render chart saat layar diputar (landscape/portrait)
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(renderChart, 300);
    });

    // --- FITUR FILTER TANGGAL TANPA RELOAD ---
    document.getElementById('date-input').addEventListener('change', function() {
        const selectedDate = this.value;
        
        // Tampilkan efek loading (opsional, biar user tahu sedang memuat)
        document.getElementById('stat-total-orders').innerText = '...';
        document.getElementById('stat-total-revenue').innerText = '...';

        fetch(`{{ route('resto.dashboard.live') }}?date=${selectedDate}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            // 1. Update Angka Statistik
            document.getElementById('stat-total-orders').innerText = data.total_orders;
            document.getElementById('stat-total-revenue').innerText = new Intl.NumberFormat('id-ID').format(data.total_revenue);

            // 2. Update Label Tanggal di Kartu (Hari Ini / 25 Okt)
            const todayStr = new Date().toISOString().split('T')[0];
            const isToday = (selectedDate === todayStr);
            
            let labelText = isToday ? 'Hari Ini' : new Date(selectedDate).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
            
            document.querySelectorAll('.date-badge-label').forEach(el => {
                el.innerText = labelText;
            });

            // 3. Update Data Grafik
            if (window.mySalesChart) {
                window.mySalesChart.data.datasets[0].data = data.chart_data;
                window.mySalesChart.update();
            }
        })
        .catch(err => {
            console.error('Gagal memuat data:', err);
            alert('Gagal memuat data tanggal.');
        });
    });
</script>
@endpush