<?php

namespace App\Http\Controllers\Resto;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request; // <-- TAMBAHKAN INI
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $restaurant = Auth::user()->restaurant;

        $selectedDate = $request->filled('date') ? $request->date : now()->toDateString();
        
        $ordersQuery = Order::where('restaurant_id', $restaurant->id)
            ->whereDate('created_at', $selectedDate);

        $totalOrders = $ordersQuery->count();
        $totalRevenue = $ordersQuery->where('status', 'done')->sum('total');
        $totalProducts = Product::where('restaurant_id', $restaurant->id)->where('is_available', true)->count();

        $chartData = Order::where('restaurant_id', $restaurant->id)
            ->where('status', 'done')
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $newOrders = Order::where('restaurant_id', $restaurant->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $doneOrders = Order::where('restaurant_id', $restaurant->id)
            ->where('status', 'done')
            ->latest()
            ->limit(5)
            ->get();

        return view('resto.dashboard', compact(
            'restaurant', 
            'totalOrders', 
            'totalRevenue', 
            'totalProducts', 
            'chartData',
            'selectedDate',
            'newOrders',
            'doneOrders'
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