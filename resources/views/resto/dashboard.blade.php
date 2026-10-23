@extends('layouts.app')

@section('title', 'Dashboard - ' . $restaurant->name)

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-extrabold">Dashboard</h2>
        <p class="text-sm text-gray-500">Ringkasan performa restoran Anda hari ini</p>
    </div>
    
    <a href="{{ route('resto.dashboard.report') }}" class="bg-teal-500 hover:bg-teal-600 text-white font-bold px-4 py-2.5 rounded-xl shadow-md shadow-teal-200 transition flex items-center gap-2 text-sm self-start md:self-auto">
        ⬇️ Download Laporan
    </a>
</div>

{{-- KARTU STATISTIK --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center text-xl">📦</div>
            <span class="text-[10px] font-bold text-teal-600 bg-teal-50 px-2 py-1 rounded-md">Hari Ini</span>
        </div>
        <p class="text-xs text-gray-500 font-semibold mb-1">Total Pesanan</p>
        <p class="text-2xl font-extrabold text-gray-800">{{ $totalOrdersToday }} <span class="text-sm font-medium text-gray-400">Pesanan</span></p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-xl">💰</div>
            <span class="text-[10px] font-bold text-orange-600 bg-orange-50 px-2 py-1 rounded-md">Hari Ini</span>
        </div>
        <p class="text-xs text-gray-500 font-semibold mb-1">Total Omzet</p>
        <p class="text-2xl font-extrabold text-gray-800">Rp {{ number_format($revenueToday, 0, ',', '.') }}</p>
    </div>

    {{-- KARTU BARU: TOTAL MENU --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-xl">🍽️</div>
            <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-md">Katalog</span>
        </div>
        <p class="text-xs text-gray-500 font-semibold mb-1">Total Menu Aktif</p>
        <p class="text-2xl font-extrabold text-gray-800">{{ $totalProducts }} <span class="text-sm font-medium text-gray-400">Item</span></p>
    </div>
</div>

{{-- GRAFIK PENJUALAN --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
    <h3 class="font-bold text-sm mb-4">Grafik Penjualan Bulan Ini ({{ date('Y') }})</h3>
    <div class="h-64 w-full">
        <canvas id="salesChart"></canvas>
    </div>
</div>

{{-- KANBAN PESANAN (Hanya Pending & Done) --}}
<div class="mb-6">
    <h3 class="font-bold text-lg mb-4">Papan Pesanan Real-Time</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- KOLOM 1: PESANAN BARU --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-orange-500 rounded-full animate-pulse"></span>
                    <h4 class="font-bold text-sm">Pesanan Baru (Menunggu Konfirmasi)</h4>
                </div>
                <span class="text-xs bg-orange-50 text-orange-600 font-bold px-2 py-1 rounded-md">{{ $newOrders->count() }}</span>
            </div>

            <div class="space-y-3">
                @forelse ($newOrders as $order)
                    <div class="border border-orange-100 bg-orange-50/50 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold bg-white text-orange-600 px-2 py-1 rounded-lg">#ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }} WIB</span>
                        </div>
                        <div class="flex justify-between items-center text-sm mb-3">
                            <span class="font-semibold text-gray-700">{{ $order->customer_name }} ({{ $order->table->name ?? '-' }})</span>
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
                    <h4 class="font-bold text-sm">Selesai (Terbaru)</h4>
                </div>
                <span class="text-xs bg-teal-50 text-teal-600 font-bold px-2 py-1 rounded-md">{{ $doneOrders->count() }}</span>
            </div>

            <div class="space-y-3">
                @forelse ($doneOrders as $order)
                    <div class="border border-teal-100 bg-teal-50/50 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold bg-white text-teal-600 px-2 py-1 rounded-lg">#ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }} WIB</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="font-semibold text-gray-700">{{ $order->customer_name }} ({{ $order->table->name ?? '-' }})</span>
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
    // Fungsi untuk merender grafik
    function renderChart() {
        const canvas = document.getElementById('salesChart');
        if (!canvas) return; // Jika canvas tidak ada, hentikan

        // Hapus instance Chart sebelumnya jika ada (biar tidak tumpang tindih)
        if (window.mySalesChart) {
            window.mySalesChart.destroy();
        }

        const ctx = canvas.getContext('2d');
        const chartData = @json($chartData);

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
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(value); }
                        }
                    }
                }
            }
        });
    }

    // 1. Jalankan saat halaman pertama kali dimuat
    document.addEventListener('DOMContentLoaded', renderChart);
    
    // 2. Jalankan saat pindah menu via Livewire
    document.addEventListener('livewire:navigated', renderChart);
</script>
@endpush