<?php

namespace App\Http\Controllers\Resto;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $restaurant = Auth::user()->restaurant;

        // 1. Statistik Hari Ini
        $totalOrdersToday = Order::where('restaurant_id', $restaurant->id)
            ->whereDate('created_at', today())
            ->count();

        $revenueToday = Order::where('restaurant_id', $restaurant->id)
            ->whereDate('created_at', today())
            ->whereIn('status', ['done', 'paid']) // Hanya hitung yang sudah selesai/lunas
            ->sum('total');

        $totalProducts = Product::where('restaurant_id', $restaurant->id)->count();

        // 2. Data Pesanan untuk Kanban (Hanya Pending & Done)
        $newOrders = Order::where('restaurant_id', $restaurant->id)
            ->where('status', 'pending')
            ->with('table')
            ->latest()
            ->get();

        $doneOrders = Order::where('restaurant_id', $restaurant->id)
            ->where('status', 'done')
            ->with('table')
            ->latest()
            ->take(5) // Ambil 5 terakhir saja agar tidak kepanjangan
            ->get();

        // 3. Data Grafik Penjualan Bulanan (Tahun Ini)
        $monthlySales = Order::where('restaurant_id', $restaurant->id)
            ->whereYear('created_at', date('Y'))
            ->whereIn('status', ['done', 'paid'])
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as total_sales')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total_sales', 'month')
            ->toArray();

        // Siapkan array 1-12 untuk Chart.js (jika bulan kosong, isi 0)
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $monthlySales[$i] ?? 0;
        }

        return view('resto.dashboard', compact(
            'restaurant', 
            'totalOrdersToday', 
            'revenueToday', 
            'totalProducts',
            'newOrders',
            'doneOrders',
            'chartData'
        ));
    }

    public function downloadReport()
    {
        $restaurant = Auth::user()->restaurant;
        $orders = Order::where('restaurant_id', $restaurant->id)
            ->whereIn('status', ['done', 'paid'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'laporan-penjualan-' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        // Header CSV
        fputcsv($handle, ['Tanggal', 'ID Pesanan', 'Nama Pelanggan', 'Metode Bayar', 'Total']);
        
        foreach ($orders as $order) {
            fputcsv($handle, [
                $order->created_at->format('Y-m-d H:i'),
                '#ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
                $order->customer_name,
                strtoupper($order->payment_method),
                $order->total
            ]);
        }
        
        fclose($handle);

        return response()->streamDownload(function () use ($handle) {}, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}