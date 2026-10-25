<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicOrderController extends Controller
{
    public function showMenu($slug, $token)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();

        $table = Table::where('restaurant_id', $restaurant->id)
            ->where('token', $token)
            ->firstOrFail();

        $categories = Category::where('restaurant_id', $restaurant->id)->get();

        $products = Product::where('restaurant_id', $restaurant->id)
            ->where('is_available', true)
             ->with(['category', 'variants' => function($query) {
                 $query->select('id', 'product_id', 'name', 'type', 'is_required'); 
            }, 'variants.options'])
            ->get();

        return view('public.menu', compact('restaurant', 'table', 'categories', 'products'));
    }

        public function submitOrder(Request $request, $slug, $token)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $table = Table::where('restaurant_id', $restaurant->id)->where('token', $token)->firstOrFail();

        $data = $request->validate([
            'customer_name'     => ['required', 'string', 'max:80'],
            'payment_method'    => ['required', 'in:qris,cash'],
            'payment_proof'     => ['nullable', 'string'],
            'cart'              => ['required', 'array', 'min:1'],
            'cart.*.product_id' => ['required', 'exists:products,id'],
            'cart.*.qty'        => ['required', 'integer', 'min:1'],
            'cart.*.variants'   => ['nullable', 'array'], // <-- TAMBAHKAN INI
            'cart.*.note'       => ['nullable', 'string'], // <-- TAMBAHKAN INI
        ]);

        // HITUNG NOMOR
        $lastNumber = Order::where('restaurant_id', $restaurant->id)->max('order_number');
        $newOrderNumber = $lastNumber ? $lastNumber + 1 : 1;

        // BUAT PESANAN (PASTI MASUK)
        $order = new Order();
        $order->tracking_token = \Illuminate\Support\Str::random(32);
        $order->order_number = $newOrderNumber; 
        $order->restaurant_id = $restaurant->id;
        $order->table_id = $table->id;
        $order->customer_name = $data['customer_name'];
        $order->status = 'pending';
        $order->payment_method = $data['payment_method'];
        $order->total = 0;
        $order->expires_at = now()->addMinutes(120);
        $order->save(); // <-- DISIMPAN DI SINI

        // SIMPAN BUKTI & ITEM
        if ($data['payment_method'] === 'qris' && !empty($data['payment_proof'])) {
            $order->payment_proof = $this->saveBase64Image($data['payment_proof'], 'proofs');
            $order->save();
        }

                $total = 0;
        foreach ($data['cart'] as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $price = $product->price;
                $qty = $item['qty'];

                // Tambahkan harga varian
                if (!empty($item['variants'])) {
                    foreach ($item['variants'] as $variant) {
                        $price += $variant['price']; // Pastikan 'price' ada di array variant
                    }
                }

                $total += $price * $qty;

                $order->items()->create([
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'price'        => $price, // Simpan harga yang sudah termasuk varian
                    'qty'          => $qty,
                    'variants'     => $item['variants'] ?? null,
                    'note'         => $item['note'] ?? null,
                ]);
            }
        }
        $order->update(['total' => $total]);

        return response()->json([
            'success'      => true,
            'order_number' => $newOrderNumber,
            'redirect_url' => url('/track/' . $order->tracking_token),
        ]);
    }


    public function reuploadProof(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        $request->validate([
            'payment_proof' => ['required', 'string'],
        ]);

        if ($order->payment_proof && Storage::disk('public')->exists($order->payment_proof)) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $order->payment_proof = $this->saveBase64Image($request->payment_proof, 'proofs');
        $order->status = 'pending';
        $order->cancel_reason = null;
        $order->updated_at = now();
        $order->save();

        return back()->with('success', 'Bukti pembayaran berhasil diunggah ulang. Mohon tunggu konfirmasi kasir.');
    }

    private function saveBase64Image($dataUrl, $folder = 'misc')
    {
        $parts = explode(',', $dataUrl);
        if (count($parts) !== 2) return null;

        $imageData = base64_decode($parts[1]);
        if ($imageData === false) return null;

        $filename = $folder . '/' . Str::random(20) . '.jpg';
        Storage::disk('public')->put($filename, $imageData);

        return $filename;
    }

    public function trackOrderSimpel($token)
    {
        // Cari berdasarkan tracking_token (bukan order_number)
        $order = Order::where('tracking_token', $token)->first();

        if (!$order) {
            return "Pesanan dengan token " . $token . " TIDAK DITEMUKAN.";
        }

        $restaurant = Restaurant::find($order->restaurant_id);

        return view('public.track', compact('order', 'restaurant'));
    }
}