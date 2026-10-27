@forelse ($orders as $order)
    @php
        $statusColor = match($order->status) {
            'pending' => 'bg-yellow-100 text-yellow-700',
            'process' => 'bg-blue-100 text-blue-700',
            'done' => 'bg-teal-100 text-teal-700',
            'cancelled' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
        $statusLabel = match($order->status) {
            'pending' => 'Menunggu Konfirmasi',
            'process' => 'Diproses Dapur',
            'done' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => 'Unknown',
        };

        $orderJson = [
            'id' => $order->id,
            'customer_name' => $order->customer_name,
            'order_number' => $order->order_number,
            'payment_method' => $order->payment_method,
            'status' => $order->status,
            'total' => $order->total,
            'updated_at' => $order->updated_at->timestamp,
            'payment_proof_url' => $order->payment_proof ? asset('storage/' . $order->payment_proof) : null,
            'updated_at' => $order->updated_at->format('d M Y, H:i'),
            'cancel_reason' => $order->cancel_reason,
            'items' => $order->items->map(function($item) {
                return [
                    'product_name' => $item->product_name,
                    'qty' => $item->qty,
                    'price' => (int) $item->price,
                    'variants' => $item->variants,
                    'note' => $item->note,
                ];
            })->toArray()
        ];
    @endphp

   {{-- CARD --}}
    <div class="order-card bg-white w-full max-w-full rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition cursor-pointer" 
        data-order-id="{{ $order->id }}" 
        data-search="ord-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }} {{ strtolower($order->customer_name) }}"
        data-order="{{ json_encode($orderJson) }}"
        onclick="window.openOrderDetail(this)">
        
        <div class="p-3 border-b border-gray-100 flex justify-between items-start w-full gap-2">
            <div class="min-w-0">
                <p class="font-extrabold text-sm text-gray-800">#ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                <p class="text-xs font-semibold text-teal-600 mt-1 truncate">{{ $order->table->name ?? '-' }}</p>
            </div>
            <span class="badge-status shrink-0 px-2.5 py-1 text-[10px] font-bold rounded-lg border {{ $statusColor }}">
                {{ $statusLabel }}
            </span>
        </div>
        
        <div class="p-3 flex-1 w-full overflow-hidden">
            <ul class="space-y-1 mb-4">
                @foreach ($order->items as $item)
                    <li class="flex justify-between items-start text-sm w-full gap-2">
                        <span class="break-words min-w-0 flex-1">{{ $item->product_name }}</span>
                        <span class="font-bold shrink-0">x{{ $item->qty }}</span>
                    </li>
                @endforeach
            </ul>
            <div class="bg-gray-50 rounded-lg p-3 mt-2 w-full overflow-hidden">
                <div class="flex justify-between items-start text-xs mb-1 gap-2">
                    <span class="text-gray-500 shrink-0">Pelanggan:</span>
                    <span class="font-bold text-right break-words min-w-0 flex-1">{{ $order->customer_name ?? '-' }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500 shrink-0">Pembayaran:</span>
                    <span class="font-bold text-teal-600 uppercase text-right break-words min-w-0 flex-1">{{ $order->payment_method ?? 'CASH' }}</span>
                </div>
            </div>
        </div>
        
        <div class="p-3 bg-gray-50 border-t border-gray-100 flex justify-between items-center w-full">
            <div class="min-w-0">
                <p class="text-[10px] text-gray-500 font-semibold uppercase">Total</p>
                <p class="font-extrabold text-teal-600 text-base truncate">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
            </div>
            <span class="shrink-0 text-xs font-bold text-teal-600 bg-teal-50 px-3 py-2 rounded-lg">Detail →</span>
        </div>
    </div>
@empty
    <div class="col-span-full py-16 text-center bg-white rounded-xl border border-dashed border-gray-300">
        <div class="text-5xl mb-3">🧾</div>
        <p class="font-bold text-gray-700">Belum ada pesanan</p>
    </div>
@endforelse