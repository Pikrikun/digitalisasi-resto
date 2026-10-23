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
        $expiredOrders = Order::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredOrders as $order) {
            $order->update([
                'status' => 'cancelled',
                'cancel_reason' => 'Batas waktu pembayaran habis'
            ]);
        }

        $this->info(count($expiredOrders) . ' pesanan dibatalkan otomatis.');
    }
}