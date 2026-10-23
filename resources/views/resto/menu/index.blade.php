@extends('layouts.app')

@section('title', 'Kelola Menu - ' . $restaurant->name)

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-extrabold">Kelola Menu</h2>
    <p class="text-sm text-gray-500">Atur daftar makanan & minuman restoran Anda</p>
</div>

@if (session('success'))
    <div class="mb-5 bg-teal-50 border border-teal-200 text-teal-700 text-sm rounded-xl px-4 py-3">
        ✅ {{ session('success') }}
    </div>
@endif

{{-- ACTION BAR --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" class="flex flex-col md:flex-row gap-3 items-stretch md:items-center">
        <div class="relative flex-1">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama menu..."
                class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none transition text-sm bg-gray-50/50">
        </div>
        <a href="{{ route('resto.menu.create') }}" wire:navigate
            class="bg-teal-500 hover:bg-teal-600 text-white font-semibold text-sm px-5 py-2.5 rounded-xl shadow-md shadow-teal-200 transition text-center whitespace-nowrap">
            + Tambah Menu Baru
        </a>
    </form>

    <div class="flex gap-2 mt-4 overflow-x-auto pb-1">
        <a href="{{ route('resto.menu.index') }}" wire:navigate
            class="text-xs font-semibold px-4 py-2 rounded-full whitespace-nowrap transition
                   {{ !request('category') || request('category') === 'semua' ? 'bg-teal-500 text-white shadow-md shadow-teal-200' : 'bg-gray-100 text-gray-600 hover:bg-teal-50' }}">
            Semua
        </a>
        @foreach ($categories as $cat)
            <a href="{{ route('resto.menu.index', ['category' => $cat->id]) }}" wire:navigate
                class="text-xs font-semibold px-4 py-2 rounded-full whitespace-nowrap transition
                       {{ request('category') == $cat->id ? 'bg-teal-500 text-white shadow-md shadow-teal-200' : 'bg-gray-100 text-gray-600 hover:bg-teal-50' }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>
</div>

{{-- GRID MENU --}}
@if ($products->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="text-6xl mb-4">🍽️</div>
        <h3 class="font-bold text-lg mb-2">Belum ada menu</h3>
        <p class="text-sm text-gray-500 mb-5">Mulai tambahkan menu makanan & minuman restoran Anda</p>
        <a href="{{ route('resto.menu.create') }}" wire:navigate
            class="inline-block bg-teal-500 hover:bg-teal-600 text-white font-semibold text-sm px-6 py-3 rounded-xl shadow-md shadow-teal-200 transition">
            + Tambah Menu Pertama
        </a>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach ($products as $p)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                <div class="aspect-square bg-gradient-to-br from-teal-50 to-green-50 flex items-center justify-center text-5xl overflow-hidden">
                    @if ($p->image)
                        <img src="{{ asset('storage/' . $p->image) }}" class="w-full h-full object-cover">
                    @else
                        🍜
                    @endif
                </div>
                <div class="p-4">
                    <div class="flex items-start justify-between mb-2">
                        <h3 class="font-bold text-sm leading-tight">{{ $p->name }}</h3>
                        @if ($p->category)
                            <span class="text-[10px] font-semibold bg-teal-50 text-teal-600 px-2 py-1 rounded-md whitespace-nowrap ml-2">{{ $p->category->name }}</span>
                        @endif
                    </div>
                    <p class="text-teal-600 font-extrabold text-base mb-3">Rp {{ number_format($p->price, 0, ',', '.') }}</p>
                    
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                        {{-- TOMBOL TOGGLE --}}
                        <button type="button" onclick="toggleAvailable({{ $p->id }}, this)" class="inline-flex items-center cursor-pointer">
                            <div class="toggle-visual relative w-10 h-6 rounded-full transition {{ $p->is_available ? 'bg-teal-500' : 'bg-gray-300' }}">
                                <div class="toggle-dot absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition {{ $p->is_available ? 'translate-x-4' : '' }}"></div>
                            </div>
                            <span class="ml-2 text-xs font-semibold toggle-text {{ $p->is_available ? 'text-teal-600' : 'text-gray-400' }}">
                                {{ $p->is_available ? 'Tersedia' : 'Habis' }}
                            </span>
                        </button>

                        {{-- TOMBOL EDIT & HAPUS --}}
                        <div class="flex gap-1">
                            <a href="{{ route('resto.menu.edit', $p->id) }}" wire:navigate class="w-8 h-8 rounded-lg hover:bg-teal-50 flex items-center justify-center transition text-sm">✏️</a>
                            <button type="button" onclick="openDeleteModal({{ $p->id }}, '{{ $p->name }}', this)" class="w-8 h-8 rounded-lg hover:bg-red-50 flex items-center justify-center transition text-sm">🗑️</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection

@push('modals')
{{-- MODAL HAPUS MENU --}}
<div id="delete-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 transform transition">
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center">
                <span class="text-3xl">🗑️</span>
            </div>
        </div>
        <h3 class="font-bold text-lg text-center mb-2">Hapus Menu?</h3>
        <p class="text-sm text-gray-500 text-center mb-6">
            Menu <span id="delete-name" class="font-semibold text-gray-800">"..."</span> akan dihapus permanen.
        </p>
        <div class="flex gap-3">
            <button type="button" onclick="closeDeleteModal()" class="flex-1 px-5 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition">Batal</button>
            <button type="button" onclick="confirmDelete()" class="flex-1 px-5 py-3 rounded-xl bg-red-500 hover:bg-red-600 text-white font-semibold text-sm shadow-md shadow-red-200 transition">Ya, Hapus</button>
        </div>
    </div>
</div>
@endpush