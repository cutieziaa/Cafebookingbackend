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
    public function index()
    {
        return Pickup::with([
            'user',
            'order.user',
            'order.items.menu',
            'order.voucher'
        ])->latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_penerima' => 'required|string',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menu,id',
            'items.*.qty' => 'required|integer|min:1',
            'voucher_code' => 'sometimes|string|exists:voucher,kode'
        ]);

        // 1. Hitung subtotal
        $total = 0;
        $itemsData = [];

        foreach ($request->items as $i) {
            $menu = Menu::findOrFail($i['menu_id']);
            $qty = $i['qty'];
            $subtotal = $menu->harga * $qty;

            $itemsData[] = [
                'menu_id' => $menu->id,
                'qty' => $qty,
                'harga' => $menu->harga,
            ];

            $total += $subtotal;
        }

        // 2. Logika Voucher (sama seperti OrderController)
        $discountAmount = 0;
        $voucherId = null;

        if ($request->filled('voucher_code')) {
            $voucher = Voucher::where('kode', $request->voucher_code)->first();

            if ($voucher) {
                $isValid = true;

                if ($voucher->expired_at && Carbon::now()->isAfter($voucher->expired_at)) {
                    $isValid = false;
                }

                if ($voucher->limit_penggunaan > 0) {
                    $usageCount = Order::where('voucher_id', $voucher->id)
                        ->whereNotIn('status', ['cancelled', 'canceled'])
                        ->count();

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
                        $discountAmount = min($voucher->diskon_nominal, $total); // ✅
                    }

                    if ($voucher->maksimum_diskon && $discountAmount > $voucher->maksimum_diskon) {
                        $discountAmount = $voucher->maksimum_diskon;
                    }
                }
            }
        }

        $finalTotal = max(0, $total - $discountAmount);

        // 3. Buat Order
        $order = Order::create([
            'user_id' => $request->user()->id,
            'jenis_order' => 'pickup',
            'total' => $finalTotal,
            'status' => 'pending',
            'voucher_id' => $voucherId,
            'discount_amount' => $discountAmount,
        ]);

        // 4. Simpan items
        foreach ($itemsData as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['menu_id'],
                'qty' => $item['qty'],
                'harga' => $item['harga'],
            ]);
        }

        // ✅ Update penggunaan voucher
        if ($voucherId && $discountAmount > 0) {
            Voucher::where('id', $voucherId)->increment('penggunaan_sekarang');
        }

        // 5. Buat Pickup
        $pickup = Pickup::create([
            'user_id' => $request->user()->id,
            'order_id' => $order->id,
            'nama_penerima' => $request->nama_penerima,
            'catatan' => $request->catatan ?? '',
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Pickup order created successfully',
            'pickup' => $pickup->load(['order.items.menu', 'order.voucher']),
            'order' => $order->load(['items.menu', 'voucher']),
            'discount' => [
                "applied" => $discountAmount > 0,
                "amount" => (float) $discountAmount,
                "final_total" => (float) $finalTotal
            ]
        ], 201);
    }

    public function myPickup(Request $request)
    {
        return Pickup::with(['order.items.menu', 'order.voucher'])
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