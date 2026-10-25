<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class CancelExpiredOrders extends Command
{
    protected $signature = 'orders:cancel-expired';
    protected $description = 'Membatalkan pesanan yang sudah melewati batas waktu pembayaran';

    public function handle()
    {
        // Cari pesanan yang statusnya 'pending' dan expires_at sudah lewat
        $expiredOrders = Order::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredOrders as $order) {
            $order->update([
                'status' => 'cancelled',
                'cancel_reason' => 'Waktu pembayaran habis (Otomatis)',
            ]);
        }
        
        $this->info('Membatalkan pesanan yang sudah melewati batas waktu.');
    }
}