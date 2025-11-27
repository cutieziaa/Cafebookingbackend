<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return Order::with('items')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_order' => 'required|in:dine_in,pickup',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menu,id',
            'items.*.qty' => 'required|integer|min:1'
        ]);

        // 1. Buat ORDER kosong dulu
        $order = Order::create([
            'user_id' => $request->user()->id,
            'jenis_order' => $request->jenis_order,
            'booking_id' => $request->booking_id,
            'total' => 0,
            'status' => 'pending'
        ]);

        $total = 0;

        // 2. Loop semua item dan ambil harga dari DB
        foreach ($request->items as $i) {

            $menu = Menu::find($i['menu_id']);
            $harga = $menu->harga;                 // harga otomatis
            $qty = $i['qty'];
            $subtotal = $harga * $qty;

            // Simpan sebagai order_items
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $menu->id,
                'qty' => $qty,
                'harga' => $harga   // TERISI OTOMATIS
            ]);

            $total += $subtotal;
        }

        // 3. Update total order
        $order->update([
            'total' => $total
        ]);

        return response()->json([
            "message" => "Order created successfully",
            "order" => $order->load('items.menu')
        ]);
    }


    public function myOrders(Request $request)
    {
        return Order::where('user_id', $request->user()->id)
            ->with('items')
            ->get();
    }

    public function markAsPaid($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'paid']);
        return $order;
    }

    public function cancel($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'cancelled']);
        return $order;
    }
}
