<?php

namespace App\Http\Controllers;

use App\Models\Pickup;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class PickupController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_penerima' => 'required|string',
            'catatan' => 'nullable|string',
            'items' => 'required|array',
            'items.*.menu_id' => 'required|integer|exists:menu,id',
            'items.*.qty' => 'required|integer|min:1'
        ]);

        // === 1. Buat pickup === //
        $pickup = Pickup::create([
            'user_id' => $request->user()->id,
            'nama_penerima' => $request->nama_penerima,
            'catatan' => $request->catatan,
            'status' => 'pending'
        ]);

        // === 2. Buat order === //
        $order = Order::create([
            'user_id' => $request->user()->id,
            'pickup_id' => $pickup->id,
            'jenis_order' => 'pickup',
            'total' => 0, // nanti dihitung ulang
            'status' => 'pending'
        ]);

        $total = 0;

        // === 3. Loop semua menu yang dipesan === //
        foreach ($request->items as $item) {
            $menu = Menu::find($item['menu_id']);
            $harga = $menu->harga;
            $subtotal = $harga * $item['qty'];

            // simpan item pesanan
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['menu_id'],
                'qty' => $item['qty'],
                'harga' => $harga
            ]);

            $total += $subtotal;
        }

        // === 4. Update total harga === //
        $order->update(['total' => $total]);

        return response()->json([
            'message' => 'Pickup order created successfully',
            'pickup' => $pickup,
            'order' => [
                'id' => $order->id,
                'total' => $order->total,
                'items' => $order->items
            ]
        ], 201);
    }


    public function myPickup(Request $request)
    {
        return Pickup::with('order.items.menu')
            ->where('user_id', $request->user()->id)
            ->get();
    }


    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,ready,completed,cancelled'
        ]);

        $pickup = Pickup::findOrFail($id);

        $pickup->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Pickup status updated',
            'pickup' => $pickup
        ]);
    }
}
