<?php

namespace App\Http\Controllers\Resto;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class TableController extends Controller
{
    public function index()
    {
        $restaurant = Auth::user()->restaurant;
        $tables = Table::where('restaurant_id', $restaurant->id)->latest()->get();

        return view('resto.table.index', compact('restaurant', 'tables'));
    }

    public function store(Request $request)
    {
        $restaurant = Auth::user()->restaurant;

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        Table::create([
            'restaurant_id' => $restaurant->id,
            'name'          => $data['name'],
            'capacity'      => $data['capacity'],
            'token'         => Str::random(32),
        ]);

        return redirect()->route('resto.table.index')
            ->with('success', 'Meja berhasil ditambahkan.');
    }

    public function destroy(Table $table)
    {
        $restaurant = Auth::user()->restaurant;

        if ($table->restaurant_id !== $restaurant->id) {
            return response()->json(['success' => false, 'message' => 'Tidak diizinkan.'], 403);
        }

        $table->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Meja dihapus.');
    }

    /**
     * Download PDF QR code standee untuk 1 meja.
     */
    public function print(Table $table)
    {
        $restaurant = Auth::user()->restaurant;

        if ($table->restaurant_id !== $restaurant->id) {
            abort(403);
        }

        $url = route('public.menu', [
            'slug'  => $restaurant->slug,
            'token' => $table->token,
        ]);

        return view('resto.table.print', compact('restaurant', 'table', 'url'));
    }
}