<?php

namespace App\Http\Controllers\Resto;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $restaurant = Auth::user()->restaurant;
        
        $query = Order::where('restaurant_id', $restaurant->id)
            ->with(['items', 'table'])
            ->orderBy('updated_at', 'desc');

        // Filter status
        if ($request->filled('status') && $request->status !== 'semua') {
            if ($request->status === 'berlangsung') {
                // Khusus CASH yang masih pending
                $query->where('status', 'pending')->where('payment_method', 'cash');
            } elseif ($request->status === 'verifikasi') {
                // Khusus QRIS yang masih pending
                $query->where('status', 'pending')->where('payment_method', 'qris');
            } else {
                $query->where('status', $request->status);
            }
        }

        $orders = $query->paginate(51); // <-- Tampilkan 10 per halaman

        return view('resto.orders.index', compact('restaurant', 'orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $restaurant = Auth::user()->restaurant;

        if ($order->restaurant_id !== $restaurant->id) {
            return response()->json(['success' => false], 403);
        }

        $request->validate([
            'status' => ['required', 'in:pending,process,done,paid,cancelled']
        ]);

        $order->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }

    public function getDetail(Order $order)
    {
        $restaurant = Auth::user()->restaurant;
        if ($order->restaurant_id !== $restaurant->id) abort(403);
        
        return response()->json([
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'customer_name' => $order->customer_name,
            'payment_method' => $order->payment_method,
            'total' => $order->total,
            // Pastikan items memuat variants dan note
            'items' => $order->items()->get()->map(function($item) {
                return [
                    'product_name' => $item->product_name,
                    'qty' => $item->qty,
                    'price' => $item->price,
                    'variants' => $item->variants, // <-- Pastikan ini terkirim
                    'note' => $item->note,         // <-- Pastikan ini terkirim
                ];
            }),
            'payment_proof_url' => $order->payment_proof ? asset('storage/' . $order->payment_proof) : null,
        ]);
    }

    public function checkNewOrders()
    {
        $restaurant = Auth::user()->restaurant;
        
        // Cari pesanan terbaru (baik pending maupun yang baru upload bukti)
        $newOrder = Order::where('restaurant_id', $restaurant->id)
            ->where('status', 'pending')
            ->latest('updated_at')
            ->orderBy('updated_at', 'desc')
            ->first();

        return response()->json([
            'count' => $newOrder ? 1 : 0,
            'latest_id' => $newOrder ? $newOrder->id : null,
            'customer_name' => $newOrder ? $newOrder->customer_name : null,
            'payment_method' => $newOrder ? $newOrder->payment_method : null,
            'status' => $newOrder ? $newOrder->status : null,
            'updated_at' => $newOrder ? $newOrder->updated_at->timestamp : null,
            'order_number' => $newOrder ? $newOrder->order_number : null, 
        ]);
    }

    public function verifyPayment(Request $request, Order $order)
    {
        $restaurant = Auth::user()->restaurant;
        
        if ($order->restaurant_id !== $restaurant->id) {
            return response()->json(['success' => false], 403);
        }

        $request->validate([
            'action' => ['required', 'in:accept,reject']
        ]);

        if ($request->action === 'accept') {
            $order->update(['status' => 'done']);
            // PASTIKAN BARIS INI ADA DAN TERSIMPAN
            if ($request->filled('cash_received')) {
                $order->cash_received = (int) $request->cash_received;
            }
            $order->save(); 
        } else {
            $order->update([
                'status' => 'cancelled',
                'cancel_reason' => $request->reason
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function getLiveOrders(Request $request)
    {
        $restaurant = Auth::user()->restaurant;
        
        $query = Order::where('restaurant_id', $restaurant->id)
            ->with(['items', 'table'])
            ->latest();

        if ($request->filled('status') && $request->status !== 'semua') {
            if ($request->status === 'berlangsung') {
                // Khusus CASH yang masih pending
                $query->where('status', 'pending')->where('payment_method', 'cash');
            } elseif ($request->status === 'verifikasi') {
                // Khusus QRIS yang masih pending
                $query->where('status', 'pending')->where('payment_method', 'qris');
            } else {
                $query->where('status', $request->status);
            }
        }

        // Tambahkan map untuk memastikan URL gambar benar
        $orders = $query->get()->map(function($order) {
            $order->payment_proof_url = $order->payment_proof ? asset('storage/' . $order->payment_proof) : null;
            return $order;
        });

        return response()->json($orders);
    }

    public function printReceipt(Order $order)
    {
        $restaurant = Auth::user()->restaurant;
        if ($order->restaurant_id !== $restaurant->id) abort(403);
        
        return view('resto.orders.print', compact('order', 'restaurant'));
    }

    public function getPartialCards(Request $request)
    {
        $restaurant = Auth::user()->restaurant;
        
        $query = Order::where('restaurant_id', $restaurant->id)
            ->with(['items', 'table'])
            ->orderBy('updated_at', 'desc');

        if ($request->filled('status') && $request->status !== 'semua') {
            if ($request->status === 'berlangsung') {
                $query->where('status', 'pending')->where('payment_method', 'cash');
            } elseif ($request->status === 'verifikasi') {
                $query->where('status', 'pending')->where('payment_method', 'qris');
            } else {
                $query->where('status', $request->status);
            }
        }

        $orders = $query->paginate(51);

        // Kembalikan HANYA view partial (isi card-nya saja)
        return view('resto.orders._cards', compact('orders'));
    }
}