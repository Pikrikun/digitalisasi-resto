@extends('layouts.public')

@section('title', $restaurant->name . ' - ' . $table->name)

@section('content')
<header class="relative bg-gradient-to-br from-teal-500 via-teal-500 to-teal-600 text-white px-5 md:px-8 pt-6 pb-8 rounded-b-[2rem] shadow-lg">
    <div class="absolute top-4 right-4 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
    <div class="absolute -bottom-8 -left-8 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>

    <div class="relative max-w-6xl mx-auto">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center ring-1 ring-white/30 overflow-hidden">
                    @if ($restaurant->logo && file_exists(storage_path('app/public/' . $restaurant->logo)))
                        <img src="{{ asset('storage/' . $restaurant->logo) }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-2xl">🍽️</span>
                    @endif
                </div>
                <div>
                    <h1 class="font-extrabold text-lg md:text-xl leading-tight">{{ $restaurant->name }}</h1>
                    <p class="text-xs text-white/80 mt-0.5">Terima kasih sudah datang 🙏</p>
                </div>
            </div>
            <div class="bg-white text-teal-600 font-extrabold text-xs px-3.5 py-2 rounded-full shadow-md whitespace-nowrap">
                {{ $table->name }}
            </div>
        </div>

        <div class="bg-white/10 backdrop-blur-sm rounded-2xl px-4 py-3 border border-white/20 mb-4">
            <p class="text-xs text-white/90 leading-relaxed">
                📱 Pilih menu yang kamu inginkan → pesanan langsung masuk ke dapur
            </p>
        </div>

        <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
            <input type="text" id="search-input" oninput="filterProducts()"
                placeholder="Cari nama menu..."
                class="w-full pl-11 pr-4 py-3 rounded-xl text-sm text-gray-800 bg-white focus:ring-2 focus:ring-white/50 outline-none shadow-md">
        </div>
    </div>
</header>

<div class="sticky top-0 z-20 bg-gray-50/95 backdrop-blur-md pt-4 pb-3 -mt-4">
    <div class="flex gap-2 overflow-x-auto scrollbar-hide px-4 md:px-8 pb-1 max-w-6xl mx-auto">
        <button onclick="filterCategory('all', this)"
            class="category-pill shrink-0 px-5 py-2.5 rounded-full text-xs font-bold transition-all bg-teal-500 text-white shadow-lg shadow-teal-200">
            🍴 Semua
        </button>
        @foreach ($categories as $cat)
            <button onclick="filterCategory({{ $cat->id }}, this)"
                data-category="{{ $cat->id }}"
                class="category-pill shrink-0 px-5 py-2.5 rounded-full text-xs font-bold transition-all bg-white text-gray-600 border border-gray-200">
                {{ $cat->name }}
            </button>
        @endforeach
    </div>
</div>

<main class="px-4 md:px-8 pt-2 max-w-6xl mx-auto">
    <div id="search-empty" class="hidden text-center py-16">
        <div class="text-6xl mb-4">🔍</div>
        <p class="font-bold text-gray-700">Menu tidak ditemukan</p>
        <p class="text-sm text-gray-400 mt-1">Coba kata kunci lain</p>
    </div>

    @if ($products->isEmpty())
        <div class="text-center py-20">
            <div class="text-7xl mb-4">🍽️</div>
            <p class="font-bold text-gray-700 text-lg">Belum ada menu</p>
            <p class="text-sm text-gray-400 mt-1">Coba cek lagi nanti ya</p>
        </div>
    @else
        @forelse ($categories as $cat)
            @php $catProducts = $products->where('category_id', $cat->id); @endphp
            @if ($catProducts->count() > 0)
                <section class="mb-8 category-section" data-category="{{ $cat->id }}">
                    <div class="mb-3">
                        <h2 class="font-extrabold text-lg md:text-xl text-gray-800 flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-teal-500 rounded-full"></span>
                            {{ $cat->name }}
                        </h2>
                        @if ($cat->description)
                            <p class="text-xs md:text-sm text-gray-500 mt-1 ml-4">{{ $cat->description }}</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
                        @foreach ($catProducts as $p)
                            <div class="product-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md border border-gray-100 active:scale-[0.97] transition-all duration-200"
                                data-category="{{ $p->category_id }}"
                                data-product-id="{{ $p->id }}"
                                data-name="{{ strtolower($p->name) }}"
                                onclick="openProduct({{ $p->id }})">
                                <div class="aspect-square bg-gradient-to-br from-teal-50 to-green-50 relative overflow-hidden">
                                    @if ($p->image && file_exists(storage_path('app/public/' . $p->image)))
                                        <img src="{{ asset('storage/' . $p->image) }}" class="w-full h-full object-cover" alt="{{ $p->name }}" loading="lazy">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-5xl md:text-6xl">🍜</div>
                                    @endif
                                </div>
                                <div class="p-3 md:p-4">
                                    <h3 class="font-bold text-sm md:text-base leading-tight mb-1.5 line-clamp-2 min-h-[2.5rem]">{{ $p->name }}</h3>
                                    <div class="flex items-center justify-between gap-2 mt-2">
                                        <p class="text-teal-600 font-extrabold text-sm md:text-base">Rp {{ number_format($p->price, 0, ',', '.') }}</p>
                                        <button type="button" 
                                            onclick="event.stopPropagation(); openProduct({{ $p->id }})" 
                                            class="bg-teal-500 hover:bg-teal-600 text-white font-bold w-8 h-8 rounded-full shadow-md flex items-center justify-center shrink-0 transition active:scale-90">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @empty
        @endforelse

        @php $noCat = $products->whereNull('category_id'); @endphp
        @if ($noCat->count() > 0)
            <section class="mb-8 category-section" data-category="none">
                <div class="mb-3">
                    <h2 class="font-extrabold text-lg md:text-xl text-gray-800 flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-gray-400 rounded-full"></span> Lainnya
                    </h2>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
                    @foreach ($noCat as $p)
                        <div class="product-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md border border-gray-100 active:scale-[0.97] transition-all duration-200"
                             data-category="none" data-name="{{ strtolower($p->name) }}" onclick="openProduct({{ $p->id }})" data-product-id="{{ $p->id }}">
                            <div class="aspect-square bg-gradient-to-br from-teal-50 to-green-50 relative overflow-hidden">
                                @if ($p->image && file_exists(storage_path('app/public/' . $p->image)))
                                    <img src="{{ asset('storage/' . $p->image) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-5xl md:text-6xl">🍜</div>
                                @endif
                            </div>
                            <div class="p-3 md:p-4">
                                <h3 class="font-bold text-sm md:text-base leading-tight mb-1.5 line-clamp-2 min-h-[2.5rem]">{{ $p->name }}</h3>
                                <div class="flex items-center justify-between gap-2 mt-2">
                                    <p class="text-teal-600 font-extrabold text-sm md:text-base">Rp {{ number_format($p->price, 0, ',', '.') }}</p>
                                    <button type="button" 
                                        onclick="event.stopPropagation(); openProduct({{ $p->id }})" 
                                        class="bg-teal-500 hover:bg-teal-600 text-white font-bold w-8 h-8 rounded-full shadow-md flex items-center justify-center shrink-0 transition active:scale-90">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    @endif
</main>

<div id="cart-bar" class="fixed bottom-4 left-4 right-4 md:left-1/2 md:-translate-x-1/2 md:max-w-md z-40 hidden slide-up">
    <div class="bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-2xl shadow-2xl shadow-teal-500/30 p-3 flex items-center justify-between ring-1 ring-white/20">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="relative shrink-0">
                <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-xl">🛒</div>
                <span id="cart-count" class="absolute -top-1.5 -right-1.5 bg-orange-500 text-white font-bold text-[10px] w-5 h-5 rounded-full flex items-center justify-center ring-2 ring-teal-500">0</span>
            </div>
            <div class="min-w-0">
                <p class="text-[9px] text-white/80 uppercase tracking-wider font-semibold mb-0.5">Total Pesanan</p>
                <p id="cart-total" class="font-extrabold text-sm sm:text-base truncate leading-tight">Rp 0</p>
            </div>
        </div>

        <button onclick="openCheckout()" 
            class="shrink-0 bg-white text-teal-600 font-extrabold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-md active:scale-95 transition flex items-center gap-1">
            Checkout <span>→</span>
        </button>
    </div>
</div>

<div id="product-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-end md:items-center justify-center">
    <div class="bg-white w-full md:max-w-md md:rounded-3xl rounded-t-[2rem] max-h-[90vh] overflow-y-auto slide-up">
        <div class="sticky top-0 bg-white z-10 flex justify-between items-center px-5 py-4 border-b border-gray-100 rounded-t-[2rem]">
            <h3 class="font-extrabold text-base" id="pm-name">Produk</h3>
            <button onclick="closeProductModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 text-xl leading-none flex items-center justify-center transition">×</button>
        </div>
        <div id="pm-body" class="p-5 space-y-5"></div>
        <div class="px-5 pb-5">
            <label class="block text-sm font-bold mb-2">Catatan <span class="text-gray-400 font-normal text-xs">(opsional)</span></label>
            <textarea id="pm-note" rows="2" placeholder="Contoh: tanpa bawang, level 5, dll"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none text-sm resize-none bg-gray-50/50"></textarea>
        </div>
        <div class="sticky bottom-0 bg-white border-t border-gray-100 p-4 flex items-center gap-3 rounded-b-[2rem]">
            <div class="flex items-center bg-gray-100 rounded-xl">
                <button onclick="changeQty(-1)" class="w-11 h-11 text-xl font-bold text-gray-600 active:scale-90 transition">−</button>
                <span id="pm-qty" class="w-10 text-center font-bold">1</span>
                <button onclick="changeQty(1)" class="w-11 h-11 text-xl font-bold text-gray-600 active:scale-90 transition">+</button>
            </div>
            <button type="button" onclick="addToCart()" class="flex-1 bg-teal-500 hover:bg-teal-600 text-white font-bold py-3.5 rounded-xl shadow-md active:scale-[0.98] transition text-sm">
                Tambah • <span id="pm-total">Rp 0</span>
            </button>
        </div>
    </div>
</div>

<div id="checkout-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-end md:items-center justify-center">
    <div class="bg-white w-full md:max-w-md md:rounded-3xl rounded-t-[2rem] max-h-[90vh] overflow-y-auto slide-up">
        <div class="sticky top-0 bg-white z-10 flex justify-between items-center px-5 py-4 border-b border-gray-100 rounded-t-[2rem]">
            <h3 class="font-extrabold text-base">Konfirmasi Pesanan</h3>
            <button onclick="closeCheckout()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 text-xl leading-none flex items-center justify-center transition">×</button>
        </div>

        <div class="p-5">
            <div id="co-items" class="space-y-3 mb-5"></div>

            <div class="border-t border-dashed border-gray-200 pt-4 mb-5">
                <div class="flex justify-between items-center">
                    <span class="font-bold text-sm text-gray-600">Total Bayar</span>
                    <span class="text-teal-600 font-extrabold text-xl" id="co-total">Rp 0</span>
                </div>
            </div>

            <label class="block text-sm font-bold mb-2">Nama Anda <span class="text-red-500">*</span></label>
            <input type="text" id="co-name" placeholder="Contoh: Budi"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 outline-none text-sm mb-5 bg-gray-50/50">

            <label class="block text-sm font-bold mb-3">Metode Pembayaran <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-2 gap-3 mb-5">
                <label class="cursor-pointer">
                    <input type="radio" name="payment_method" value="cash" class="peer hidden" onchange="changePayment('cash')" checked>
                    <div class="border-2 border-gray-200 peer-checked:border-teal-500 peer-checked:bg-teal-50 rounded-xl p-4 text-center transition">
                        <div class="text-3xl mb-2">💵</div>
                        <p class="font-bold text-sm">Bayar di Kasir</p>
                        <p class="text-[10px] text-gray-500 mt-1">Cash</p>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="payment_method" value="qris" class="peer hidden" onchange="changePayment('qris')">
                    <div class="border-2 border-gray-200 peer-checked:border-teal-500 peer-checked:bg-teal-50 rounded-xl p-4 text-center transition">
                        <div class="text-3xl mb-2">📱</div>
                        <p class="font-bold text-sm">QRIS</p>
                        <p class="text-[10px] text-gray-500 mt-1">Transfer / e-wallet</p>
                    </div>
                </label>
            </div>

            <div id="qris-section" class="hidden mb-5">
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-4">
                    <p class="text-xs font-bold text-gray-700 mb-3 text-center">📱 Scan QRIS di bawah untuk bayar</p>
                    <div id="qris-image-container" class="relative">
                        @if ($restaurant->qris_image && file_exists(storage_path('app/public/' . $restaurant->qris_image)))
                            <img id="qris-img" src="{{ asset('storage/' . $restaurant->qris_image) }}" class="w-full max-w-[240px] mx-auto rounded-xl bg-white p-2 shadow-sm">
                        @else
                            <div class="w-full max-w-[240px] mx-auto aspect-square bg-white rounded-xl flex items-center justify-center text-5xl shadow-sm">📱</div>
                            <p class="text-xs text-center text-orange-500 mt-2">QRIS belum di-setup</p>
                        @endif
                    </div>
                    @if ($restaurant->qris_image && file_exists(storage_path('app/public/' . $restaurant->qris_image)))
                        <button type="button" onclick="downloadQris()" class="w-full mt-3 bg-white hover:bg-gray-50 text-teal-600 font-semibold text-xs px-3 py-2.5 rounded-lg border-2 border-teal-200 transition flex items-center justify-center gap-2">
                            ⬇️ Download QRIS
                        </button>
                    @endif
                </div>

                @if ($restaurant->qris_owner_name)
                    <div class="text-center mb-4">
                        <p class="text-xs text-gray-500 mb-1">Atas Nama:</p>
                        <p class="font-extrabold text-lg text-gray-800">{{ $restaurant->qris_owner_name }}</p>
                    </div>
                @endif

                <div class="bg-teal-50 border border-teal-200 rounded-xl p-4 mb-4 text-center">
                    <p class="text-xs font-semibold text-teal-700 uppercase tracking-wider mb-1">Total yang harus dibayar</p>
                    <div class="flex items-center justify-center gap-2">
                        <p class="text-2xl font-extrabold text-teal-600" id="qris-total-amount">Rp 0</p>
                        <button type="button" onclick="copyNominal()" class="bg-white hover:bg-gray-100 text-teal-600 text-xs font-bold px-3 py-1.5 rounded-lg border border-teal-200 transition shadow-sm">
                            Salin
                        </button>
                    </div>
                </div>

                <label class="block text-sm font-bold mb-2">Upload Bukti Transfer <span class="text-red-500">*</span></label>
                <label for="proof-input" class="block border-2 border-dashed border-gray-300 rounded-xl p-4 text-center cursor-pointer hover:border-teal-400 transition">
                    <div id="proof-preview">
                        <div class="text-3xl mb-1">📷</div>
                        <p class="text-xs font-semibold">Klik untuk upload bukti</p>
                        <p class="text-[10px] text-gray-400">JPG, PNG</p>
                    </div>
                </label>
                <input type="file" id="proof-input" accept="image/*" class="hidden" onchange="previewProof(event)">
            </div>

            <div id="cash-section" class="bg-teal-50 border border-teal-100 rounded-xl p-4 mb-5">
                <p class="text-xs font-bold text-teal-700 mb-1">💵 Bayar di Kasir</p>
                <p class="text-xs text-teal-600 leading-relaxed">Silakan bayar langsung ke kasir atau pelayan. Pesanan diproses setelah dikonfirmasi.</p>
            </div>
        </div>

        <div class="sticky bottom-0 bg-white border-t border-gray-100 p-4 rounded-b-[2rem]">
            <button onclick="submitOrder()" id="btn-submit" class="w-full bg-teal-500 hover:bg-teal-600 text-white font-bold py-4 rounded-xl shadow-md active:scale-[0.98] transition">
                Kirim Pesanan
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showToast(message, type = 'error') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-4 left-1/2 -translate-x-1/2 z-[9999] flex flex-col gap-2 pointer-events-none w-full max-w-sm px-4';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    let borderClass = type === 'success' ? 'border-teal-500' : 'border-red-500';
    let icon = type === 'success' ? '✅' : '❌';

    toast.className = `bg-white text-gray-800 border-l-4 ${borderClass} shadow-2xl rounded-r-xl p-4 flex items-start gap-3 pointer-events-auto transform transition-all duration-300 -translate-y-10 opacity-0`;
    toast.innerHTML = `
        <span class="text-xl">${icon}</span>
        <div class="flex-1 text-sm font-semibold pt-0.5">${message}</div>
        <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 text-lg leading-none">&times;</button>
    `;
    container.appendChild(toast);
    setTimeout(() => toast.classList.remove('-translate-y-10', 'opacity-0'), 10);
    setTimeout(() => {
        toast.classList.add('-translate-y-10', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

const PRODUCTS = {!! json_encode($products->map(function ($p) {
    return [
        'id'       => $p->id,
        'name'     => $p->name,
        'price'    => (int) $p->price,
        'image'    => $p->image && file_exists(storage_path('app/public/' . $p->image)) ? asset('storage/' . $p->image) : null,
        'variants' => $p->variants->map(function ($v) {
            return [
                'id'      => $v->id,
                'name'    => $v->name,
                'type'    => $v->type ?? 'single',
                'is_required' => $v->is_required,
                'options' => $v->options->map(function ($o) {
                    return [
                        'id'    => $o->id,
                        'name'  => $o->name,
                        'price' => (int) $o->additional_price,
                    ];
                }),
            ];
        }),
    ];
})) !!};

selectedProduct = null;
selectedQty = 1;
selectedVariants = {};
proofDataUrl = null;
cart = [];
localStorage.removeItem('cart');

function filterProducts() {
    const query = document.getElementById('search-input').value.toLowerCase().trim();
    const activeCat = document.querySelector('.category-pill.bg-teal-500')?.dataset.category || 'all';
    let visibleCount = 0;

    document.querySelectorAll('.category-section').forEach(section => {
        const catId = section.dataset.category;
        const catMatch = (activeCat === 'all' || catId == activeCat);
        let sectionVisible = 0;

        section.querySelectorAll('.product-card').forEach(card => {
            const nameMatch = card.dataset.name.includes(query);
            if (nameMatch && catMatch) {
                card.style.display = '';
                sectionVisible++;
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        section.style.display = (catMatch && sectionVisible > 0) ? '' : 'none';
    });

    const noResult = document.getElementById('search-empty');
    if (visibleCount === 0) noResult.classList.remove('hidden');
    else noResult.classList.add('hidden');
}

function filterCategory(catId, btn) {
    document.querySelectorAll('.category-pill').forEach(el => {
        el.classList.remove('bg-teal-500', 'text-white', 'shadow-lg', 'shadow-teal-200');
        el.classList.add('bg-white', 'text-gray-600', 'border', 'border-gray-200');
    });
    btn.classList.remove('bg-white', 'text-gray-600', 'border', 'border-gray-200');
    btn.classList.add('bg-teal-500', 'text-white', 'shadow-lg', 'shadow-teal-200');
    filterProducts();
}

function openProduct(id) {
    const p = PRODUCTS.find(x => x.id === id);
    if (!p) return;

    selectedProduct = p;
    selectedQty = 1;
    selectedVariants = {};

    document.getElementById('pm-name').textContent = p.name;
    document.getElementById('pm-qty').textContent = '1';
    document.getElementById('pm-note').value = '';

    let html = '';
    if (p.image) {
        html += `<div class="rounded-2xl overflow-hidden aspect-square bg-gray-50"><img src="${p.image}" class="w-full h-full object-cover"></div>`;
    } else {
        html += `<div class="rounded-2xl aspect-square bg-gradient-to-br from-teal-50 to-green-50 flex items-center justify-center text-7xl">🍜</div>`;
    }

    html += `<div class="flex items-baseline justify-between"><div><p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Harga</p><p class="text-2xl font-extrabold text-teal-600">Rp ${formatRp(p.price)}</p></div></div>`;

    if (p.variants && p.variants.length > 0) {
        p.variants.forEach((v, vi) => {
            const isMultiple = v.type === 'multiple';
            console.log('Varian:', v.name, '| Wajib:', v.is_required);
            const typeBadge = isMultiple 
                ? `<span class="text-[10px] bg-blue-100 text-blue-600 px-2 py-0.5 rounded ml-2">Bisa pilih banyak</span>` 
                : `<span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded ml-2">Pilih 1</span>`;
            
            html += `<div><p class="font-bold text-sm mb-3 flex items-center gap-2"><span class="w-1.5 h-4 bg-teal-500 rounded-full"></span>${v.name} ${typeBadge}</p><div class="space-y-2">`;
            
            v.options.forEach(o => {
                const inputType = isMultiple ? 'checkbox' : 'radio';
                const inputName = isMultiple ? `variant-${vi}[]` : `variant-${vi}`;
                const onChangeAction = isMultiple 
                    ? `onchange="toggleOptionalVariant(this, ${vi}, '${v.name}', '${o.name}', ${o.price})"` 
                    : `onchange="pickVariant(${vi}, '${v.name}', '${o.name}', ${o.price})"`;

                html += `
                    <label class="flex items-center justify-between bg-gray-50 hover:bg-teal-50 has-[:checked]:bg-teal-50 has-[:checked]:ring-2 has-[:checked]:ring-teal-400 rounded-xl px-4 py-3 cursor-pointer transition ring-1 ring-gray-100">
                        <div class="flex items-center gap-3">
                            <input type="${inputType}" name="${inputName}" value="${o.id}" ${onChangeAction} class="w-4 h-4 text-teal-500 focus:ring-teal-300">
                            <span class="text-sm font-medium">${o.name}</span>
                        </div>
                        ${o.price > 0 ? `<span class="text-xs font-bold text-teal-600 bg-teal-50 px-2 py-1 rounded-md">+Rp ${formatRp(o.price)}</span>` : `<span class="text-xs text-gray-400 font-semibold">Gratis</span>`}
                    </label>
                `;
            });
            html += `</div></div>`;
        });
    }

    document.getElementById('pm-body').innerHTML = html;
    updateProductTotal();

    const modal = document.getElementById('product-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeProductModal() {
    const modal = document.getElementById('product-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function pickVariant(groupIndex, groupName, optionName, price) {
    selectedVariants[groupIndex] = { group: groupName, option: optionName, price: price };
    updateProductTotal();
}

function changeQty(delta) {
    selectedQty = Math.max(1, selectedQty + delta);
    document.getElementById('pm-qty').textContent = selectedQty;
    updateProductTotal();
}

function updateProductTotal() {
    if (!selectedProduct) return;
    let total = selectedProduct.price;
    
    Object.values(selectedVariants).forEach(v => {
        if (v.options) {
            v.options.forEach(opt => total += opt.price);
        } else {
            total += v.price;
        }
    });
    
    total *= selectedQty;
    document.getElementById('pm-total').textContent = 'Rp ' + formatRp(total);
}

function addToCart() {
    if (!selectedProduct) return;

    // ==========================================
    // VALIDASI VARIAN WAJIB (MENGIKUTI DATABASE)
    // ==========================================
    if (selectedProduct.variants) {
        for (let i = 0; i < selectedProduct.variants.length; i++) {
            const variant = selectedProduct.variants[i];
            
            // Cek apakah varian ini WAJIB (is_required = true/1)
            const isRequired = variant.is_required === true || variant.is_required == 1;

            if (isRequired) {
                const isPicked = selectedVariants[i];
                let hasPicked = false;

                if (isPicked) {
                    if (isPicked.options) {
                        hasPicked = isPicked.options.length > 0;
                    } else {
                        hasPicked = true;
                    }
                }

                // Jika wajib tapi belum ada yang dipilih, HENTIKAN!
                if (!hasPicked) {
                    showToast(`Wajib pilih ${variant.name} terlebih dahulu!`, 'error');
                    return; 
                }
            }
        }
    }

    // ==========================================
    // JIKA LOLOS VALIDASI, SIMPAN KE KERANJANG
    // ==========================================
    let variantArray = [];
    Object.values(selectedVariants).forEach(v => {
        if (v.options) {
            v.options.forEach(opt => {
                variantArray.push({ group: v.group, option: opt.option, price: opt.price });
            });
        } else {
            variantArray.push(v);
        }
    });

    let price = selectedProduct.price;
    variantArray.forEach(v => price += v.price);
    const note = document.getElementById('pm-note').value.trim();

    cart.push({
        product_id: selectedProduct.id,
        name: selectedProduct.name,
        price: price,
        qty: selectedQty,
        variants: variantArray,
        note: note,
    });
    
    updateCartBar();
    closeProductModal();
}

function updateCartBar() {
    const count = cart.reduce((sum, x) => sum + x.qty, 0);
    const total = cart.reduce((sum, x) => sum + (x.price * x.qty), 0);
    document.getElementById('cart-count').textContent = count;
    document.getElementById('cart-total').textContent = 'Rp ' + formatRp(total);
    const bar = document.getElementById('cart-bar');
    if (cart.length > 0) bar.classList.remove('hidden');
    else bar.classList.add('hidden');
}

function toggleOptionalVariant(checkbox, groupIndex, groupName, optionName, price) {
    if (!selectedVariants[groupIndex]) {
        selectedVariants[groupIndex] = { group: groupName, options: [] };
    }

    if (checkbox.checked) {
        selectedVariants[groupIndex].options.push({ option: optionName, price: price });
    } else {
        selectedVariants[groupIndex].options = selectedVariants[groupIndex].options.filter(o => o.option !== optionName);
        if (selectedVariants[groupIndex].options.length === 0) {
            delete selectedVariants[groupIndex];
        }
    }
    
    updateProductTotal();
}

function openCheckout() {
    if (cart.length === 0) return;
    renderCheckoutItems();
    const modal = document.getElementById('checkout-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function renderCheckoutItems() {
    let html = '';
    cart.forEach((item, i) => {
        let variantText = '';
        if (item.variants && item.variants.length > 0) {
            variantText = '<p class="text-xs text-gray-400 mt-0.5">' + item.variants.map(v => v.option).join(' • ') + '</p>';
        }
        let noteText = '';
        if (item.note) {
            noteText = `<p class="text-xs text-orange-500 mt-1 italic">📝 ${item.note}</p>`;
        }
        html += `
            <div class="bg-gray-50 rounded-xl p-3">
                <div class="flex justify-between items-start gap-3 mb-2">
                    <div class="flex-1">
                        <p class="font-bold text-sm">${item.name}</p>
                        ${variantText}
                        ${noteText}
                        <p class="text-xs text-gray-500 mt-1 font-semibold">Rp ${formatRp(item.price)}</p>
                    </div>
                    <p class="font-extrabold text-sm text-teal-600 whitespace-nowrap">Rp ${formatRp(item.price * item.qty)}</p>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                    <div class="flex items-center gap-1">
                        <button onclick="changeCartQty(${i}, -1)" class="w-8 h-8 bg-white rounded-lg font-bold text-gray-600 border border-gray-200 active:scale-90 transition">−</button>
                        <span class="w-8 text-center font-bold text-sm">${item.qty}</span>
                        <button onclick="changeCartQty(${i}, 1)" class="w-8 h-8 bg-white rounded-lg font-bold text-gray-600 border border-gray-200 active:scale-90 transition">+</button>
                    </div>
                    <button onclick="removeFromCart(${i})" class="text-xs font-semibold text-red-500 hover:text-red-600 px-3 py-1.5">🗑️ Hapus</button>
                </div>
            </div>
        `;
    });
    document.getElementById('co-items').innerHTML = html;
    const total = cart.reduce((sum, x) => sum + (x.price * x.qty), 0);
    document.getElementById('co-total').textContent = 'Rp ' + formatRp(total);
    updateQrisTotal();
    updateCartBar();
}

function changeCartQty(index, delta) {
    cart[index].qty = Math.max(1, cart[index].qty + delta);
    renderCheckoutItems();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    if (cart.length === 0) {
        closeCheckout();
        updateCartBar();
        return;
    }
    renderCheckoutItems();
}

function closeCheckout() {
    const modal = document.getElementById('checkout-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function changePayment(method) {
    const qrisSection = document.getElementById('qris-section');
    const cashSection = document.getElementById('cash-section');
    if (method === 'qris') {
        qrisSection.classList.remove('hidden');
        cashSection.classList.add('hidden');
        updateQrisTotal();
    } else {
        qrisSection.classList.add('hidden');
        cashSection.classList.remove('hidden');
    }
}

function updateQrisTotal() {
    const el = document.getElementById('qris-total-amount');
    if (!el) return;
    const total = cart.reduce((sum, x) => sum + (x.price * x.qty), 0);
    el.textContent = 'Rp ' + formatRp(total);
    el.dataset.raw = total;
}

function copyNominal() {
    const el = document.getElementById('qris-total-amount');
    const raw = el.dataset.raw || '0';
    const btn = event.currentTarget;
    if (raw === '0') { showToast('Total pembayaran masih kosong.', 'error'); return; }
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(raw).then(() => { showCopiedState(btn); }).catch(() => { fallbackCopy(raw, btn); });
    } else {
        fallbackCopy(raw, btn);
    }
}

function fallbackCopy(text, btn) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed"; 
    textArea.style.left = "-999999px";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        const successful = document.execCommand('copy');
        if (successful) showCopiedState(btn);
        else showToast('Gagal menyalin. Silakan ketik manual: ' + text, 'error');
    } catch (err) {
        showToast('Gagal menyalin. Silakan ketik manual: ' + text, 'error');
    }
    document.body.removeChild(textArea);
}

function showCopiedState(btn) {
    const original = btn.innerHTML;
    const originalClass = btn.className;
    btn.innerHTML = '✓ Tersalin';
    btn.className = btn.className.replace('text-teal-600', 'text-green-600').replace('bg-white', 'bg-green-50');
    setTimeout(() => {
        btn.innerHTML = original;
        btn.className = originalClass;
    }, 1500);
}

function downloadQris() {
    const img = document.getElementById('qris-img');
    if (!img) return showToast('QRIS tidak tersedia', 'error');
    const a = document.createElement('a');
    a.href = img.src;
    a.download = 'QRIS-{{ $restaurant->slug }}.png';
    a.click();
}

function previewProof(event) {
    const file = event.target.files[0];
    if (!file) return;
    if (!file.type.startsWith('image/')) { showToast('File harus gambar.', 'error'); return; }
    if (file.size > 5 * 1024 * 1024) { showToast('Maks 5MB.', 'error'); return; }
    const reader = new FileReader();
    reader.onload = function(e) {
        proofDataUrl = e.target.result;
        document.getElementById('proof-preview').innerHTML = '<img src="' + proofDataUrl + '" class="w-full max-h-48 object-contain rounded-lg"><p class="text-xs text-teal-600 font-semibold mt-2">✓ Bukti terpasang</p>';
    };
    reader.readAsDataURL(file);
}

function submitOrder() {
    const name = document.getElementById('co-name').value.trim();
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

    if (!name) { showToast('Isi nama Anda dulu ya', 'error'); return; }
    if (paymentMethod === 'qris' && !proofDataUrl) { showToast('Upload bukti transfer dulu ya', 'error'); return; }

    const btn = document.getElementById('btn-submit');
    btn.disabled = true;
    btn.textContent = 'Mengirim...';

    fetch('{{ route("public.order", ["slug" => $restaurant->slug, "token" => $table->token]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            customer_name: name,
            payment_method: paymentMethod,
            payment_proof: proofDataUrl,
            cart: cart.map(x => ({
                product_id: x.product_id,
                qty: x.qty,
                variants: x.variants,
                note: x.note || null,
            })),
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Langsung ikuti URL dari server (yang berisi tracking_token acak)
            window.location.href = data.redirect_url;
        } else {
            alert('ERROR DARI SERVER: ' + (data.message || JSON.stringify(data)));
            btn.disabled = false;
            btn.textContent = 'Kirim Pesanan';
        }
    })
    .catch(error => {
        alert('ERROR JAVASCRIPT: ' + error);
        btn.disabled = false;
        btn.textContent = 'Kirim Pesanan';
    });
}

function formatRp(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

    // ==========================================
    // SENSOR REAL-TIME: SEMBUNYIKAN MENU YANG HABIS
    // ==========================================
    setInterval(function() {
        // Jangan jalankan sensor kalau pelanggan sedang buka modal (biar ga kaget)
        const isModalOpen = !document.getElementById('product-modal').classList.contains('hidden') || 
                            !document.getElementById('checkout-modal').classList.contains('hidden');
        if (isModalOpen) return;

        fetch('/api/check-menu-status/{{ $restaurant->slug }}')
            .then(res => res.json())
            .then(data => {
                const availableIds = data.available_ids;
                
                document.querySelectorAll('.product-card').forEach(card => {
                    const productId = parseInt(card.getAttribute('data-product-id'));
                    
                    if (!productId) return;

                    // Jika ID produk TIDAK ADA di daftar available_ids (artinya habis)
                    if (!availableIds.includes(productId)) {
                        // Sembunyikan dengan animasi halus
                        if (card.style.display !== 'none') {
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.9)';
                            
                            setTimeout(() => {
                                card.style.display = 'none';
                            }, 300);
                        }
                    } else {
                        // Jika sebelumnya disembunyikan tapi sekarang tersedia lagi, munculkan
                        if (card.style.display === 'none') {
                            card.style.display = '';
                            setTimeout(() => {
                                card.style.opacity = '1';
                                card.style.transform = 'scale(1)';
                            }, 10);
                        }
                    }
                });
            })
            .catch(err => console.log('Sensor menu gagal:', err));
    }, 5000); // Cek setiap 5 detik
</script>
@endpush