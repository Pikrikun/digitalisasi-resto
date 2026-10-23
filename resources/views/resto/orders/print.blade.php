<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        /* Desain khusus printer thermal */
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            width: 58mm; /* Standar kertas struk */
            margin: 0 auto;
            padding: 10px;
            color: #000;
        }
        .text-center { text-align: center; }
        .line { border-bottom: 1px dashed #000; margin: 5px 0; }
        .title { font-size: 16px; font-weight: bold; margin-bottom: 2px; }
        .bold { font-weight: bold; }
        
        /* Sembunyikan tombol print saat dicetak */
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="text-center">
        <div class="title">{{ $restaurant->name }}</div>
        <div>{{ $restaurant->address ?? 'Alamat belum diatur' }}</div>
        <div>Telp: {{ $restaurant->phone ?? '-' }}</div>
    </div>

    <div class="line"></div>

    <div>
        <div>No   : #ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</div>
        <div>Tgl  : {{ $order->created_at->format('d/m/Y H:i') }}</div>
        <div>Meja : {{ $order->table->name ?? '-' }}</div>
        <div>Nama : {{ $order->customer_name }}</div>
    </div>

    <div class="line"></div>

    {{-- Rincian Item + Varian + Catatan --}}
    @foreach ($order->items as $item)
        <div style="margin-bottom: 5px;">
            <div class="bold">{{ $item->product_name }}</div>
            
            @if ($item->variants)
                <div style="font-size: 10px;">
                    Varian: {{ collect($item->variants)->pluck('option')->join(', ') }}
                </div>
            @endif

            @if ($item->note)
                <div style="font-size: 10px; font-style: italic;">
                    * {{ $item->note }}
                </div>
            @endif

            <div style="display: flex; justify-content: space-between;">
                <span>{{ $item->qty }} x {{ number_format($item->price, 0, ',', '.') }}</span>
                <span>{{ number_format($item->price * $item->qty, 0, ',', '.') }}</span>
            </div>
        </div>
    @endforeach

    <div class="line"></div>

    {{-- Total --}}
    <div style="display: flex; justify-content: space-between; font-size: 14px;" class="bold">
        <span>TOTAL</span>
        <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
    </div>
    
        {{-- LOGIKA PEMBAYARAN --}}
    @if ($order->payment_method === 'cash')
        {{-- Jika uang tunai yang diinput kasir ada --}}
        @if ($order->cash_received)
            <div style="display: flex; justify-content: space-between; font-size: 11px; margin-top: 5px;">
                <span>Tunai</span>
                <span>Rp {{ number_format($order->cash_received, 0, ',', '.') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 11px;">
                <span>Kembali</span>
                <span>Rp {{ number_format($order->cash_received - $order->total, 0, ',', '.') }}</span>
            </div>
        @else
            <div style="display: flex; justify-content: space-between; font-size: 11px; margin-top: 5px;">
                <span>Bayar ({{ strtoupper($order->payment_method) }})</span>
                <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        @endif
    @else
        {{-- Jika pembayaran menggunakan QRIS --}}
        <div style="display: flex; justify-content: space-between; font-size: 11px; margin-top: 5px;">
            <span>Bayar (QRIS)</span>
            <span>LUNAS</span>
        </div>
    @endif

    <div class="line"></div>

    <div class="text-center" style="margin-top: 10px;">
        <div>Terima Kasih!</div>
        <div>Silakan Datang Kembali</div>
    </div>

    {{-- Tombol Print (Hanya muncul di layar, tidak dicetak) --}}
    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px; background: #14b8a6; color: white; border: none; border-radius: 5px; width: 100%;">Cetak Ulang</button>
    </div>

</body>
</html>