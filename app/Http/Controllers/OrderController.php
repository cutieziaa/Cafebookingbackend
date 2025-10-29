<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        return view('customer.order', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'jumlah' => 'required|integer|min:1',
            'tipe_order' => 'required|in:pickup,dine-in',
        ]);

        Order::create([
            'user_id' => auth()->id(),
            'menu_id' => $request->menu_id,
            'jumlah' => $request->jumlah,
            'tipe_order' => $request->tipe_order,
        ]);

        return redirect()->route('order.history')->with('success', 'Pesanan berhasil dibuat!');
    }

    public function history()
    {
        $orders = Order::with('menu')->where('user_id', auth()->id())->latest()->get();
        return view('customer.order_history', compact('orders'));
    }
}
