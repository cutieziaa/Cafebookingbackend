<?php

namespace App\Http\Controllers;

use App\Models\Pickup;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Voucher;
use Carbon\Carbon;
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
            'items.*.qty' => 'required|integer|min:1',
            'voucher_code' => 'sometimes|string|exists:voucher,kode' // Tambahkan ini
        ]);

        // 1. Buat ORDER
        $order = Order::create([
            'user_id' => $request->user()->id,
            'jenis_order' => 'pickup',
            'total' => 0,
            'status' => 'pending'
        ]);

        $total = 0;

        // 2. Hitung items
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

        // 3. LOGIKA VOUCHER (sama seperti OrderController)
        $discountAmount = 0;
        $voucherId = null;

        if ($request->filled('voucher_code')) {
            $voucher = Voucher::where('kode', $request->voucher_code)->first();

            if ($voucher) {
                $isValid = true;

                // Validasi voucher
                if ($voucher->expired_at && Carbon::now()->isAfter($voucher->expired_at)) {
                    $isValid = false;
                }

                if ($voucher->limit_penggunaan > 0) {
                    $usageCount = Order::where('voucher_id', $voucher->id)->count();
                    if ($usageCount >= $voucher->limit_penggunaan) {
                        $isValid = false;
                    }
                }

                if ($total < $voucher->minimum_order) {
                    $isValid = false;
                }

                if ($isValid) {
                    $voucherId = $voucher->id;
                    if ($voucher->diskon_persen) {
                        $discountAmount = $total * ($voucher->diskon_persen / 100);
                    } elseif ($voucher->diskon_nominal) {
                        $discountAmount = $voucher->diskon_nominal;
                    }
                }
            }
        }

        $finalTotal = $total - $discountAmount;

        // 4. Update order dengan voucher
        $order->update([
            'total' => $finalTotal,
            'voucher_id' => $voucherId,
            'discount_amount' => $discountAmount,
        ]);

        // 5. Buat Pickup
        $pickup = Pickup::create([
            'user_id' => $request->user()->id,
            'order_id' => $order->id,
            'nama_penerima' => $request->nama_penerima,
            'catatan' => $request->catatan,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Pickup order created successfully',
            'pickup'  => $pickup->load('order.items.menu', 'order.voucher'),
            'order'   => $order->load('items.menu', 'voucher')
        ], 201);
    }

    // ... method lainnya tetap sama


        // === 3. Buat Pickup === //
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

        $pickup->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Pickup status updated',
            'pickup' => $pickup
        ]);
    }
}
