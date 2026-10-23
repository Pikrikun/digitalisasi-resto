@extends('layouts.app')

@section('title', 'Edit Menu - ' . $restaurant->name)

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<style>
    .upload-box.dragover { border-color: #14b8a6; background-color: #f0fdfa; }
</style>
@endpush

@section('content')
<div class="mb-6">
    <a href="{{ route('resto.menu.index') }}" wire:navigate class="text-sm text-teal-600 hover:underline">← Kembali ke Kelola Menu</a>
    <h2 class="text-2xl font-extrabold mt-2">Edit Menu</h2>
    <p class="text-sm text-gray-500">Lengkapi detail menu yang akan ditampilkan ke pelanggan</p>
</div>

@if ($errors->any())
    <div class="mb-5 bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('resto.menu.update', $product->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KIRI: FOTO --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-bold text-sm mb-4">Foto Menu</h3>

                <label for="image-input"
                    class="upload-box block border-2 border-dashed border-gray-300 rounded-xl aspect-square flex flex-col items-center justify-center text-center cursor-pointer hover:border-teal-400 transition relative overflow-hidden">

                    <div id="preview-container" class="w-full h-full flex flex-col items-center justify-center">
                        @if ($product->image && file_exists(storage_path('app/public/' . $product->image)))
                            <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover rounded-lg">
                        @else
                            <div class="text-5xl mb-3">📸</div>
                            <p class="text-sm font-semibold">Klik untuk upload foto</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG, maks 5MB</p>
                        @endif
                    </div>
                </label>

                <input id="image-input" type="file" name="image" accept="image/*" class="hidden" onchange="openCropModal(event)">
                <input type="hidden" name="cropped_image_data" id="cropped-image-data">

                <p class="text-xs text-gray-400 mt-3 text-center">Foto yang menarik meningkatkan pesanan</p>
            </div>
        </div>

        {{-- KANAN: DETAIL + VARIAN --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- DETAIL MENU --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-bold text-sm mb-4">Detail Menu</h3>

                <div class="space-y-4">
                    {{-- Nama Menu --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2">
                            Nama Menu <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                            placeholder="Contoh: Ayam Goreng Sambal Matah"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none transition text-sm bg-gray-50/50">
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2">Kategori</label>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <select name="category_id" id="category-select"
                                class="flex-1 px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none transition text-sm bg-gray-50/50">
                                <option value="">— Tanpa Kategori —</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" data-description="{{ $cat->description }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" onclick="openCategoryModal()"
                                class="px-4 py-3 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-600 font-semibold text-sm transition whitespace-nowrap">
                                + Kategori
                            </button>
                            <button type="button" onclick="openDeleteCategoryModal()"
                                class="px-4 py-3 rounded-xl bg-red-50 hover:bg-red-100 text-red-500 font-semibold text-sm transition whitespace-nowrap">
                                ✏️
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Klik ✏️ untuk mengedit kategori yang salah</p>
                    </div>

                    {{-- Harga --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2">
                            Harga Utama (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input  name="price" value="{{ old('price', (int) $product->price) }}" required min="0"
                            placeholder="35000" type="number"
                            class="w-full px-4  py-3 rounded-xl border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none transition text-sm bg-gray-50/50">
                        <p class="text-xs text-gray-400 mt-1">Harga dasar sebelum tambahan varian</p>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_available" value="1" {{ old('is_available', $product->is_available) ? 'checked' : '' }}
                                class="w-5 h-5 rounded border-gray-300 text-teal-500 focus:ring-teal-300">
                            <span class="text-sm font-semibold">Tersedia sekarang</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- VARIAN --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="font-bold text-sm">Atur Varian & Tambahan</h3>
                        <p class="text-xs text-gray-400 mt-1">
                            Opsional. Harga tambahan boleh dikosongkan.
                        </p>
                    </div>
                    <button type="button" onclick="addVariantGroup()"
                        class="px-4 py-2 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-600 font-semibold text-xs transition whitespace-nowrap">
                        + Grup Varian
                    </button>
                </div>

                <div id="variant-container" class="space-y-4">
                    @if ($product->variants->isEmpty())
                        <p id="variant-empty" class="text-xs text-gray-400 text-center py-6 border-2 border-dashed border-gray-200 rounded-xl">
                            Belum ada varian. Klik "+ Grup Varian" untuk menambahkan.
                        </p>
                    @else
                        @foreach ($product->variants as $i => $variant)
                            <div class="variant-group border border-gray-200 rounded-xl p-4 bg-gray-50/50" data-index="{{ $i }}">
                                <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 mb-3">
                                    <input type="text" name="variants[{{ $i }}][name]" value="{{ $variant->name }}"
                                        placeholder="Nama grup (misal: Tingkat Kepedasan)"
                                        class="w-full sm:flex-1 px-3 py-2 rounded-lg border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none text-sm bg-white mb-2 sm:mb-0">
                                    
                                    {{-- Checkbox Wajib --}}
                                    <label class="flex items-center gap-2 px-3 py-2 bg-white rounded-lg border border-gray-200 cursor-pointer whitespace-nowrap">
                                        <input type="checkbox" name="variants[{{ $i }}][is_required]" value="1" {{ $variant->is_required ? 'checked' : '' }} class="w-4 h-4 text-teal-500 rounded focus:ring-teal-400">
                                        <span class="text-xs font-semibold text-gray-600">Wajib</span>
                                    </label>

                                    {{-- Dropdown Tipe Varian --}}
                                    <select name="variants[{{ $i }}][type]" class="px-3 py-2 rounded-lg border border-gray-200 text-xs bg-white focus:ring-teal-100 outline-none">
                                        <option value="single" {{ $variant->type === 'single' ? 'selected' : '' }}>Pilih 1 (Radio)</option>
                                        <option value="multiple" {{ $variant->type === 'multiple' ? 'selected' : '' }}>Pilih Banyak (Checkbox)</option>
                                    </select>

                                    <button type="button" onclick="removeVariantGroup(this)"
                                        class="w-9 h-9 rounded-lg hover:bg-red-50 text-red-500 flex items-center justify-center transition">
                                        🗑️
                                    </button>
                                </div>
                                <div class="variant-options space-y-2 pl-3 border-l-2 border-teal-200">
                                    @foreach ($variant->options as $j => $opt)
                                            <div class="variant-option flex flex-wrap sm:flex-nowrap items-center gap-2">
                                            <input type="text" name="variants[{{ $i }}][options][{{ $j }}][name]" value="{{ $opt->name }}"
                                                placeholder="Nama opsi"
                                                class="flex-1 px-3 py-2 rounded-lg border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none text-xs bg-white">

                                            <div class="relative w-36">
                                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-semibold pointer-events-none">+Rp</span>
                                                <input type="number" name="variants[{{ $i }}][options][{{ $j }}][price]" value="{{ (int) $opt->additional_price }}"
                                                    placeholder="0"
                                                    class="w-full pl-11 pr-3 py-2 rounded-lg border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none text-xs bg-white">
                                            </div>

                                            <button type="button" onclick="removeVariantOption(this)"
                                                class="w-8 h-8 rounded-lg hover:bg-red-50 text-red-400 flex items-center justify-center transition text-sm">
                                                ×
                                            </button>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" onclick="addVariantOption(this)"
                                    class="mt-3 text-xs font-semibold text-teal-600 hover:text-teal-700">
                                    + Tambah Opsi
                                </button>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 mt-6 pb-10">
        <a href="{{ route('resto.menu.index') }}"
            class="px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition text-center">
            Batal
        </a>
        <button type="submit"
            class="px-6 py-3 rounded-xl bg-teal-500 hover:bg-teal-600 text-white font-semibold text-sm shadow-md shadow-teal-200 transition">
            Simpan Perubahan
        </button>
    </div>

</form>

{{-- ============ MODAL KATEGORI ============ --}}
<div id="category-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-lg">Tambah Kategori Baru</h3>
            <button type="button" onclick="closeCategoryModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">×</button>
        </div>

        <form id="category-form" onsubmit="submitCategory(event)">
            <div class="mb-5">
                <label class="block text-sm font-semibold mb-2">Nama Kategori</label>
                <input type="text" id="category-name" required
                    placeholder="Contoh: Makanan, Minuman, Camilan"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none transition text-sm">
                <p id="category-error" class="text-xs text-red-500 mt-1 hidden"></p>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold mb-2">Deskripsi <span class="text-gray-400 font-normal text-xs">(opsional)</span></label>
                <input type="text" id="category-description"
                    placeholder="Contoh: Aneka mie & nasi goreng"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none transition text-sm">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeCategoryModal()"
                    class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-600 text-white font-semibold text-sm shadow-md shadow-teal-200 transition">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============ MODAL CROP FOTO ============ --}}
<div id="crop-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-lg">Sesuaikan Foto</h3>
            <button type="button" onclick="closeCropModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">×</button>
        </div>

        <div class="bg-gray-900 p-4">
            <div class="aspect-square w-full overflow-hidden rounded-lg">
                <img id="crop-image" src="" alt="crop" class="max-w-full block">
            </div>
        </div>

        <div class="px-6 py-3 bg-teal-50 text-teal-700 text-xs font-medium text-center">
            💡 Geser & zoom foto untuk mengatur area yang ditampilkan. Rasio 1:1 (kotak).
        </div>

        <div class="flex justify-end gap-2 px-6 py-4 border-t border-gray-100">
            <button type="button" onclick="closeCropModal()"
                class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition">
                Batal
            </button>
            <button type="button" onclick="applyCrop()"
                class="px-5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-600 text-white font-semibold text-sm shadow-md shadow-teal-200 transition">
                Terapkan
            </button>
        </div>
    </div>
</div>

{{-- ============ TEMPLATE VARIAN ============ --}}
<template id="variant-group-template">
    <div class="variant-group border border-gray-200 rounded-xl p-4 bg-gray-50/50" data-index="">
                <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 mb-3">
            {{-- Input Nama Grup --}}
            <input type="text" name="variants[INDEX][name]"
                placeholder="Nama grup (misal: Tingkat Kepedasan)"
                class="w-full sm:flex-1 px-3 py-2 rounded-lg border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none text-sm bg-white mb-2 sm:mb-0">
            
            {{-- Toggle Wajib Diisi --}}
            <label class="flex items-center gap-2 px-3 py-2 bg-white rounded-lg border border-gray-200 cursor-pointer whitespace-nowrap">
                <input type="checkbox" name="variants[INDEX][is_required]" value="1" class="w-4 h-4 text-teal-500 rounded focus:ring-teal-400">
                <span class="text-xs font-semibold text-gray-600">Wajib </span>
            </label>
            
            {{-- Pilihan Tipe Varian --}}
            <select name="variants[INDEX][type]" class="px-3 py-2 rounded-lg border border-gray-200 text-xs bg-white focus:ring-teal-100 outline-none">
                <option value="single">Pilih 1 (Radio)</option>
                <option value="multiple">Pilih Banyak (Checkbox)</option>
            </select>

            {{-- Tombol Hapus Grup --}}
            <button type="button" onclick="removeVariantGroup(this)"
                class="w-9 h-9 rounded-lg hover:bg-red-50 text-red-500 flex items-center justify-center transition">
                🗑️
            </button>
        </div>

        <div class="variant-options space-y-2 pl-3 border-l-2 border-teal-200"></div>

        <button type="button" onclick="addVariantOption(this)"
            class="mt-3 text-xs font-semibold text-teal-600 hover:text-teal-700">
            + Tambah Opsi
        </button>

    </div>
</template>

<template id="variant-option-template">
    <div class="variant-option flex flex-wrap sm:flex-nowrap items-center gap-2">
        <input type="text" name="variants[INDEX][options][OPTION_INDEX][name]"
            placeholder="Nama opsi (misal: Pedas)"
            class="w-full sm:flex-1 px-3 py-2 rounded-lg border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none text-xs bg-white mb-2 sm:mb-0">
        <div class="relative w-28 sm:w-36 shrink-0">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-semibold pointer-events-none">+Rp</span>
            <input type="number" name="variants[INDEX][options][OPTION_INDEX][price]"
                placeholder="0"
                class="w-full pl-11 pr-3 py-2 rounded-lg border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none text-xs bg-white">
        </div>

        <button type="button" onclick="removeVariantOption(this)"
            class="w-8 h-8 rounded-lg hover:bg-red-50 text-red-400 flex items-center justify-center transition text-sm">
            ×
        </button>
    </div>
</template>

{{-- ============ MODAL HAPUS KATEGORI ============ --}}
<div id="delete-category-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[60] hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-lg text-red-600">Kelola Kategori</h3>
            <button type="button" onclick="closeDeleteCategoryModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">×</button>
        </div>

        <p class="text-sm text-gray-600 mb-5">
            Kelola kategori restoran Anda. Klik edit untuk mengubah nama/deskripsi, atau hapus untuk menghapus.
        </p>

        <div id="delete-category-list" class="space-y-2 max-h-72 overflow-y-auto mb-5">
            {{-- Diisi oleh JS --}}
        </div>

        <div class="flex justify-end">
            <button type="button" onclick="closeDeleteCategoryModal()"
                class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- ============ MODAL EDIT KATEGORI ============ --}}
<div id="edit-category-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[70] hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-lg">Edit Kategori</h3>
            <button type="button" onclick="closeEditCategoryModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">×</button>
        </div>

        <form id="edit-category-form" onsubmit="submitEditCategory(event)">
            <input type="hidden" id="edit-category-id">

            <div class="mb-5">
                <label class="block text-sm font-semibold mb-2">Nama Kategori</label>
                <input type="text" id="edit-category-name" required
                    placeholder="Contoh: Makanan"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none transition text-sm">
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold mb-2">Deskripsi <span class="text-gray-400 font-normal text-xs">(opsional)</span></label>
                <input type="text" id="edit-category-description"
                    placeholder="Contoh: Aneka mie & nasi goreng"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none transition text-sm">
                <p id="edit-category-error" class="text-xs text-red-500 mt-1 hidden"></p>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEditCategoryModal()"
                    class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-600 text-white font-semibold text-sm shadow-md shadow-teal-200 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============ MODAL KONFIRMASI HAPUS KATEGORI ============ --}}
<div id="confirm-delete-category-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[80] hidden items-center justify-center p-4">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 transform transition">
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center">
                <span class="text-3xl">🗑️</span>
            </div>
        </div>
        <h3 class="font-bold text-lg text-center mb-2">Hapus Kategori?</h3>
        <p class="text-sm text-gray-500 text-center mb-6">
            Kategori <span id="confirm-delete-category-name" class="font-semibold text-gray-800"></span> akan dihapus. Menu dengan kategori ini akan menjadi <strong>Tanpa Kategori</strong>.
        </p>
        <div class="flex gap-3">
            <button type="button" onclick="closeConfirmDeleteCategory()" class="flex-1 px-5 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition">Batal</button>
            <button type="button" onclick="executeDeleteCategory()" class="flex-1 px-5 py-3 rounded-xl bg-red-500 hover:bg-red-600 text-white font-semibold text-sm shadow-md shadow-red-200 transition">Ya, Hapus</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script>
// LOGIKA JS SAMA PERSIS SEPERTI ASLINYA
function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('preview-container').innerHTML =
            '<img src="' + e.target.result + '" class="w-full h-full object-cover rounded-lg">';
    };
    reader.readAsDataURL(file);
}

function openCategoryModal() {
    const modal = document.getElementById('category-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('category-name').value = '';
    document.getElementById('category-description').value = '';
    document.getElementById('category-error').classList.add('hidden');
    setTimeout(() => document.getElementById('category-name').focus(), 100);
}

function closeCategoryModal() {
    const modal = document.getElementById('category-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function submitCategory(e) {
    e.preventDefault();
    const name = document.getElementById('category-name').value.trim();
    const description = document.getElementById('category-description').value.trim();
    const errorEl = document.getElementById('category-error');
    if (!name) return;

    fetch('{{ route('resto.categories.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ name: name, description: description })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('category-select');
            const option = document.createElement('option');
            option.value = data.category.id;
            option.textContent = data.category.name;
            option.dataset.description = data.category.description || '';
            option.selected = true;
            select.appendChild(option);

            document.getElementById('category-name').value = '';
            document.getElementById('category-description').value = '';

            closeCategoryModal();
        } else {
            errorEl.textContent = data.message || 'Gagal menyimpan kategori.';
            errorEl.classList.remove('hidden');
        }
    })
    .catch(() => {
        errorEl.textContent = 'Terjadi kesalahan. Coba lagi.';
        errorEl.classList.remove('hidden');
    });
}

let variantIndex = {{ $product->variants->count() }};

function addVariantGroup() {
    const container = document.getElementById('variant-container');
    const empty = document.getElementById('variant-empty');
    if (empty) empty.remove();

    const index = container.querySelectorAll('.variant-group').length;
    const template = document.getElementById('variant-group-template').innerHTML;
    const html = template.replaceAll('INDEX', index);

    const div = document.createElement('div');
    div.innerHTML = html;
    const groupEl = div.firstElementChild;
    groupEl.dataset.index = index;

    container.appendChild(groupEl);
    addVariantOption(groupEl.querySelector('button[onclick="addVariantOption(this)"]'));
}

function removeVariantGroup(btn) {
    const groupEl = btn.closest('.variant-group');
    const container = document.getElementById('variant-container');
    groupEl.remove();

    const groups = container.querySelectorAll('.variant-group');
    groups.forEach((g, i) => {
        g.dataset.index = i;
        g.querySelectorAll('input[name^="variants"]').forEach(input => {
            input.name = input.name.replace(
                /variants\[[^\]]+\]/,
                `variants[${i}]`
            );
        });
        reindexOptions(g);
    });

    if (groups.length === 0) {
        container.innerHTML = '<p id="variant-empty" class="text-xs text-gray-400 text-center py-6 border-2 border-dashed border-gray-200 rounded-xl">Belum ada varian. Klik "+ Grup Varian" untuk menambahkan.</p>';
    }
}

function removeVariantOption(btn) {
    const groupEl = btn.closest('.variant-group');
    btn.closest('.variant-option').remove();
    reindexOptions(groupEl);
}

function addVariantOption(btn) {
    const groupEl = btn.closest('.variant-group');
    const groupIndex = groupEl.dataset.index;
    const optionsContainer = groupEl.querySelector('.variant-options');
    const optionIndex = optionsContainer.querySelectorAll('.variant-option').length;

    const template = document.getElementById('variant-option-template').innerHTML;
    let html = template
        .replaceAll('INDEX', groupIndex)
        .replaceAll('OPTION_INDEX', optionIndex);

    const div = document.createElement('div');
    div.innerHTML = html;
    optionsContainer.appendChild(div.firstElementChild);

    reindexOptions(groupEl);
}

function reindexOptions(groupEl) {
    const groupIndex = groupEl.dataset.index;
    const options = groupEl.querySelectorAll('.variant-option');

    options.forEach((opt, i) => {
        opt.querySelectorAll('input').forEach(input => {
            input.name = input.name.replace(
                /variants\[[^\]]+\]\[options\]\[[^\]]+\]/,
                `variants[${groupIndex}][options][${i}]`
            );
        });
    });
}

function openDeleteCategoryModal() {
    const select = document.getElementById('category-select');
    const listEl = document.getElementById('delete-category-list');
    listEl.innerHTML = '';

    const options = Array.from(select.options).filter(opt => opt.value !== '');

    if (options.length === 0) {
        listEl.innerHTML = '<p class="text-sm text-gray-400 text-center py-6">Belum ada kategori.</p>';
    } else {
        options.forEach(opt => {
            const item = document.createElement('div');
            item.className = 'flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3';
            item.innerHTML = `
                <span class="text-sm font-medium flex-1">${opt.textContent}</span>
                <div class="flex gap-1">
                    <button type="button" onclick="openEditCategoryModal(${opt.value})"
                        class="w-8 h-8 rounded-lg hover:bg-teal-100 text-teal-600 flex items-center justify-center transition text-sm">
                        ✏️
                    </button>
                    <button type="button" onclick="deleteCategory(${opt.value}, this)"
                        class="w-8 h-8 rounded-lg hover:bg-red-100 text-red-500 flex items-center justify-center transition text-sm">
                        🗑️
                    </button>
                </div>
            `;
            listEl.appendChild(item);
        });
    }

    const modal = document.getElementById('delete-category-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeDeleteCategoryModal() {
    const modal = document.getElementById('delete-category-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function deleteCategory(id, btn) {
    if (!confirm('Yakin ingin menghapus kategori ini?')) return;

    fetch('{{ url('/resto/categories') }}/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('category-select');
            const option = select.querySelector('option[value="' + id + '"]');
            if (option) option.remove();

            btn.closest('div').remove();

            const listEl = document.getElementById('delete-category-list');
            if (listEl.children.length === 0) {
                listEl.innerHTML = '<p class="text-sm text-gray-400 text-center py-6">Belum ada kategori untuk dihapus.</p>';
            }
        } else {
            alert(data.message || 'Gagal menghapus kategori.');
        }
    })
    .catch(() => alert('Terjadi kesalahan. Coba lagi.'));
}

function openEditCategoryModal(categoryId) {
    const select = document.getElementById('category-select');
    const option = select.querySelector('option[value="' + categoryId + '"]');

    if (!option) return;

    closeDeleteCategoryModal(); // PENTING: Tutup modal hapus dulu

    document.getElementById('edit-category-id').value = categoryId;
    document.getElementById('edit-category-name').value = option.textContent;
    document.getElementById('edit-category-description').value = option.dataset.description || '';
    document.getElementById('edit-category-error').classList.add('hidden');

    const modal = document.getElementById('edit-category-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEditCategoryModal() {
    const modal = document.getElementById('edit-category-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function submitEditCategory(e) {
    e.preventDefault();

    const id = document.getElementById('edit-category-id').value;
    const name = document.getElementById('edit-category-name').value.trim();
    const description = document.getElementById('edit-category-description').value.trim();
    const errorEl = document.getElementById('edit-category-error');

    if (!name) return;

    fetch('{{ url('/resto/categories') }}/' + id, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ name: name, description: description })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('category-select');
            const option = select.querySelector('option[value="' + id + '"]');
            if (option) {
                option.textContent = data.category.name;
                option.dataset.description = data.category.description || '';
            }

            closeEditCategoryModal();
            openDeleteCategoryModal(); // Buka lagi modal hapus (biar list terupdate)
        } else {
            errorEl.textContent = data.message || 'Gagal update kategori.';
            errorEl.classList.remove('hidden');
        }
    })
    .catch(() => {
        errorEl.textContent = 'Terjadi kesalahan. Coba lagi.';
        errorEl.classList.remove('hidden');
    });
}

let cropper = null;

function openCropModal(event) {
    const file = event.target.files[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        alert('File harus berupa gambar.');
        event.target.value = '';
        return;
    }
    if (file.size > 5 * 1024 * 1024) {
        alert('Ukuran foto maksimal 5MB.');
        event.target.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = document.getElementById('crop-image');
        img.src = e.target.result;

        const modal = document.getElementById('crop-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        img.onload = function() {
            if (cropper) cropper.destroy();
            cropper = new Cropper(img, {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 1,
                movable: true,
                zoomable: true,
                rotatable: false,
                scalable: false,
                background: false,
            });
        };
    };
    reader.readAsDataURL(file);
}

function closeCropModal() {
    const modal = document.getElementById('crop-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    document.getElementById('image-input').value = '';
}

function applyCrop() {
    if (!cropper) return;

    const canvas = cropper.getCroppedCanvas({
        width: 800,
        height: 800,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
    });

    const dataUrl = canvas.toDataURL('image/jpeg', 0.9);

    document.getElementById('cropped-image-data').value = dataUrl;

    document.getElementById('preview-container').innerHTML =
        '<img src="' + dataUrl + '" class="w-full h-full object-cover rounded-lg">';

    document.getElementById('image-input').value = '';
    closeCropModal();
}
</script>
@endpush