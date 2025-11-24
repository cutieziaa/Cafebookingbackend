<?php

namespace App\Http\Controllers;

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
        $order = Order::create([
            'user_id' => $request->user()->id,
            'booking_id' => $request->booking_id,
            'pickup_id' => $request->pickup_id,
            'jenis_order' => $request->jenis_order,
            'total' => $request->total,
        ]);

        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['menu_id'],
                'qty' => $item['qty'],
                'harga' => $item['harga'],
            ]);
        }

        return $order->load('items');
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
