<?php

namespace App\Http\Controllers\Resto;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $restaurant = Auth::user()->restaurant;

        $categories = Category::where('restaurant_id', $restaurant->id)->get();

        $query = Product::where('restaurant_id', $restaurant->id)->with('category');

        // Filter kategori
        if ($request->filled('category') && $request->category !== 'semua') {
            $query->where('category_id', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->get();

        return view('resto.menu.index', compact('restaurant', 'categories', 'products'));
    }
    public function create()
{
    $restaurant = Auth::user()->restaurant;
    $categories = Category::where('restaurant_id', $restaurant->id)->get();

    return view('resto.menu.create', compact('restaurant', 'categories'));
}

public function store(Request $request)
{
    $restaurant = Auth::user()->restaurant;

    $data = $request->validate([
        'name'         => ['required', 'string', 'max:150'],
        'category_id'  => ['nullable', 'exists:categories,id'],
        'price'        => ['required', 'numeric', 'min:0'],
        'is_available' => ['nullable', 'boolean'],
    ]);

    $data['restaurant_id'] = $restaurant->id;
    $data['is_available']  = $request->boolean('is_available');

    if ($request->filled('cropped_image_data')) {
        $data['image'] = $this->saveBase64Image($request->cropped_image_data);
    }

    $product = Product::create($data);

    $product->variants()->delete();
    
    // Simpan varian
    $this->saveVariants($product, $request->input('variants', []));

    return redirect()->route('resto.menu.index')
        ->with('success', 'Menu berhasil ditambahkan.');
}

// Method bantu simpan varian
// Method bantu simpan varian
private function saveVariants(Product $product, array $variants)
{
    foreach ($variants as $variantData) {
        // Skip kalau nama grup kosong
        if (empty($variantData['name'])) continue;

        // Paksa cek is_required
        $isRequired = 0;
        if (isset($variantData['is_required'])) {
            if ($variantData['is_required'] == 1 || $variantData['is_required'] == '1' || $variantData['is_required'] == 'on') {
                $isRequired = 1;
            }
        }

        // Paksa ambil type, default 'single'
        $type = isset($variantData['type']) && in_array($variantData['type'], ['single', 'multiple']) ? $variantData['type'] : 'single';

        $variant = $product->variants()->create([
            'name' => $variantData['name'],
            'is_required' => $isRequired,
            'type' => $type, // <-- Gunakan variabel $type yang sudah dideklarasikan
        ]);

        if (!empty($variantData['options'])) {
            foreach ($variantData['options'] as $opt) {
                if (empty($opt['name'])) continue;

                $variant->options()->create([
                    'name'             => $opt['name'],
                    'additional_price' => $opt['price'] ?? 0,
                ]);
            }
        }
    }
}

// Method bantu untuk simpan base64
private function saveBase64Image($dataUrl)
{
    // Format: "data:image/jpeg;base64,/9j/4AAQ..."
    $parts = explode(',', $dataUrl);
    if (count($parts) !== 2) return null;

    $imageData = base64_decode($parts[1]);
    if ($imageData === false) return null;

    $filename = 'products/' . uniqid() . '.jpg';
    \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $imageData);

    return $filename;
}

public function storeCategory(Request $request)
{
    $restaurant = Auth::user()->restaurant;

    $data = $request->validate([
        'name'        => ['required', 'string', 'max:50'],
        'description' => ['nullable', 'string', 'max:150'],
    ]);

    $category = Category::create([
        'restaurant_id' => $restaurant->id,
        'name'          => $data['name'],
        'description'   => $data['description'] ?? null,
    ]);

    if ($request->expectsJson()) {
        return response()->json([
            'success'  => true,
            'category' => $category,
        ]);
    }

    return back()->with('success', 'Kategori berhasil ditambahkan.');
}

public function destroyCategory(Category $category)
{
    $restaurant = Auth::user()->restaurant;

    // Pastikan kategori milik restoran ini (keamanan)
    if ($category->restaurant_id !== $restaurant->id) {
        return response()->json(['success' => false, 'message' => 'Tidak diizinkan.'], 403);
    }

    $category->delete();

    if (request()->expectsJson()) {
        return response()->json(['success' => true]);
    }

    return back()->with('success', 'Kategori dihapus.');
}
public function edit(Product $product)
{
    $restaurant = Auth::user()->restaurant;

    if ($product->restaurant_id !== $restaurant->id) {
        abort(403);
    }

    // Load varian + opsi
    $product->load('variants.options');

    $categories = Category::where('restaurant_id', $restaurant->id)->get();

    return view('resto.menu.edit', compact('restaurant', 'product', 'categories'));
}

   public function update(Request $request, Product $product)
{
    $restaurant = Auth::user()->restaurant;

    if ($product->restaurant_id !== $restaurant->id) {
        abort(403);
    }

    $data = $request->validate([
        'name'         => ['required', 'string', 'max:150'],
        'category_id'  => ['nullable', 'exists:categories,id'],
        'price'        => ['required', 'numeric', 'min:0'],
        'description'  => ['nullable', 'string'],
        'is_available' => ['nullable', 'boolean'],
    ]);

    $data['is_available'] = $request->boolean('is_available');

    if ($request->filled('cropped_image_data')) {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        $data['image'] = $this->saveBase64Image($request->cropped_image_data);
    }

    $product->update($data);

    // Hapus varian lama
    $product->variants()->delete();
    
    // Simpan varian baru menggunakan method bantu saveVariants yang sudah diperbaiki
    $this->saveVariants($product, $request->input('variants', []));

    return redirect()->route('resto.menu.index')
        ->with('success', 'Menu berhasil diperbarui.');
}


public function destroy(Product $product)
{
    $restaurant = Auth::user()->restaurant;

    if ($product->restaurant_id !== $restaurant->id) {
        return response()->json(['success' => false, 'message' => 'Tidak diizinkan.'], 403);
    }

    // Hapus gambar dari storage
    if ($product->image && Storage::disk('public')->exists($product->image)) {
        Storage::disk('public')->delete($product->image);
    }

    $product->delete();

    if (request()->expectsJson()) {
        return response()->json(['success' => true]);
    }

    return back()->with('success', 'Menu berhasil dihapus.');
}

public function toggleAvailable(Product $product)
{
    $restaurant = Auth::user()->restaurant;

    if ($product->restaurant_id !== $restaurant->id) {
        return response()->json(['success' => false], 403);
    }

    $product->update(['is_available' => !$product->is_available]);

    return response()->json([
        'success'      => true,
        'is_available' => $product->is_available,
    ]);
}

public function updateCategory(Request $request, Category $category)
{
    $restaurant = Auth::user()->restaurant;

    // Keamanan
    if ($category->restaurant_id !== $restaurant->id) {
        return response()->json(['success' => false, 'message' => 'Tidak diizinkan.'], 403);
    }

    $data = $request->validate([
        'name'        => ['required', 'string', 'max:50'],
        'description' => ['nullable', 'string', 'max:150'],
    ]);

    $category->update([
        'name'        => $data['name'],
        'description' => $data['description'] ?? null,
    ]);

    return response()->json([
        'success'  => true,
        'category' => $category,
    ]);
}

}