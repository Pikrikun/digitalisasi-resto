@extends('layouts.app')

@section('title', 'Cetak QR Meja - ' . $restaurant->name)

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-extrabold">Cetak QR Meja</h2>
    <p class="text-sm text-gray-500">Kelola meja & cetak QR code untuk ditempel di meja pelanggan</p>
</div>

@if (session('success'))
    <div class="mb-5 bg-teal-50 border border-teal-200 text-teal-700 text-sm rounded-xl px-4 py-3">
        ✅ {{ session('success') }}
    </div>
@endif

{{-- FORM TAMBAH MEJA --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
    <form method="POST" action="{{ route('resto.table.store') }}" class="flex flex-col md:flex-row gap-3">
        @csrf
        <input type="text" name="name" required placeholder="Nomor/Nama Meja (misal: Meja 05)"
            class="flex-1 px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none text-sm bg-gray-50/50">
        <select name="capacity" required class="md:w-56 px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none text-sm bg-gray-50/50">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ $i == 4 ? 'selected' : '' }}>Kapasitas {{ $i }} Orang</option>
            @endfor
        </select>
        <button type="submit" class="px-6 py-3 rounded-xl bg-teal-500 hover:bg-teal-600 text-white font-semibold text-sm shadow-md shadow-teal-200 transition whitespace-nowrap">
            + Tambah Meja Baru
        </button>
    </form>
</div>

{{-- GRID MEJA --}}
@if ($tables->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="text-6xl mb-4">🪑</div>
        <h3 class="font-bold text-lg mb-2">Belum ada meja</h3>
        <p class="text-sm text-gray-500">Tambahkan meja pertama untuk mulai mencetak QR code</p>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($tables as $t)
            @php
                $qrUrl = route('public.menu', ['slug' => $restaurant->slug, 'token' => $t->token]);
            @endphp

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                <div class="p-6 bg-gradient-to-br from-teal-50 to-green-50 flex flex-col items-center">
                    <div class="bg-white p-3 rounded-xl shadow-sm" id="qr-wrapper-{{ $t->id }}">
                        {!! QrCode::format('svg')->size(140)->margin(1)->generate($qrUrl) !!}
                    </div>
                    <div class="mt-4 text-center">
                        <p class="font-bold text-gray-800">{{ $t->name }}</p>
                        <p class="text-xs text-gray-500">Scan untuk pesan menu</p>
                    </div>
                </div>

                <div class="p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-bold">{{ $t->name }}</h3>
                            <p class="text-xs text-gray-400">Kapasitas {{ $t->capacity }} orang</p>
                        </div>
                        <span class="text-[10px] font-semibold bg-teal-50 text-teal-600 px-2 py-1 rounded-md whitespace-nowrap">Aktif</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('resto.table.print', $t->id) }}" target="_blank"
                            class="bg-teal-500 hover:bg-teal-600 text-white font-semibold text-xs px-3 py-2.5 rounded-lg shadow-sm transition text-center">
                            🖨️ Cetak
                        </a>
                        <button type="button" onclick="downloadPng({{ $t->id }}, '{{ $t->name }}')"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-semibold text-xs px-3 py-2.5 rounded-lg shadow-sm transition text-center">
                            ⬇️ PNG
                        </button>
                    </div>

                    {{-- TOMBOL HAPUS MEMANGGIL FUNGSI DARI RESTO.JS --}}
                    <button type="button" onclick="openDeleteTableModal({{ $t->id }}, '{{ $t->name }}')"
                        class="w-full mt-2 border-2 border-red-200 text-red-500 hover:bg-red-50 font-semibold text-xs px-3 py-2.5 rounded-lg transition">
                        🗑️ Hapus
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection

@push('modals')
{{-- MODAL HAPUS MEJA --}}
<div id="delete-table-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center">
                <span class="text-3xl">🗑️</span>
            </div>
        </div>
        <h3 class="font-bold text-lg text-center mb-2">Hapus Meja?</h3>
        <p class="text-sm text-gray-500 text-center mb-6">
            Meja <span id="delete-table-name" class="font-semibold text-gray-800"></span> akan dihapus permanen.
        </p>
        <div class="flex gap-3">
            <button type="button" onclick="closeDeleteTableModal()" class="flex-1 px-5 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition">Batal</button>
            <button type="button" onclick="confirmDeleteTable()" class="flex-1 px-5 py-3 rounded-xl bg-red-500 hover:bg-red-600 text-white font-semibold text-sm shadow-md shadow-red-200 transition">Ya, Hapus</button>
        </div>
    </div>
</div>
@endpush