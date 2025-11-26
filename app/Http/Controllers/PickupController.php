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
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menu,id',
            'items.*.qty' => 'required|integer|min:1'
        ]);

        // === 1. Buat ORDER dulu === //
        $order = Order::create([
            'user_id' => $request->user()->id,
            'jenis_order' => 'pickup',
            'pickup_id' => null,    // di-update setelah pickup dibuat
            'total' => 0,
            'status' => 'pending'
        ]);

        $total = 0;

        // === 2. Simpan semua Items === //
        foreach ($request->items as $i) {
            $menu = Menu::find($i['menu_id']);
            $subtotal = $menu->harga * $i['qty'];

            OrderItem::create([
                'order_id' => $order->id,
                'menu_id'  => $menu->id,
                'qty'      => $i['qty'],
                'harga'    => $menu->harga
            ]);

            $total += $subtotal;
        }

        // Update total order
        $order->update(['total' => $total]);


        // === 3. Buat Pickup dan hubungkan dengan order === //
        $pickup = Pickup::create([
            'user_id'        => $request->user()->id,
            'order_id'       => $order->id,     // PENTING!
            'nama_penerima'  => $request->nama_penerima,
            'catatan'        => $request->catatan,
            'status'         => 'pending'
        ]);

        // hubungkan kembali ke order
        $order->update(['pickup_id' => $pickup->id]);


        return response()->json([
            'message' => 'Pickup order created successfully',
            'pickup'  => $pickup,
            'order'   => [
                'id'    => $order->id,
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
