{{-- resources/views/resto/orders/_list.blade.php --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse ($orders as $order)
        {{-- (Kode card pesanan & modal detail milikmu yang panjang, biarkan sama persis) --}}
        <div onclick="openOrderDetail({{ $order->id }})" class="bg-white rounded-xl shadow-sm ...">
            {{-- ... isi card ... --}}
        </div>
        {{-- ... modal detail ... --}}
    @empty
        <div class="col-span-full py-16 text-center ...">
            <p class="font-bold text-gray-700">Belum ada pesanan</p>
        </div>
    @endforelse
</div>