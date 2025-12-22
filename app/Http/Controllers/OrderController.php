<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index()
    {
        return Order::with(['items.menu', 'user', 'voucher', 'pickup'])
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_order' => 'required|in:dine_in,pickup',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menu,id',
            'items.*.qty' => 'required|integer|min:1',
            'voucher_code' => 'sometimes|string|exists:voucher,kode',
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

        // 2. Logika Voucher (diperbaiki)
        $discountAmount = 0;
        $voucherId = null;

        if ($request->filled('voucher_code')) {
            $voucher = Voucher::where('kode', $request->voucher_code)->first();

            if ($voucher) {
                $isValid = true;

                // a. Cek kadaluarsa
                if ($voucher->expired_at && Carbon::now()->isAfter($voucher->expired_at)) {
                    $isValid = false;
                }

                // b. Cek limit penggunaan — hanya order aktif
                if ($voucher->limit_penggunaan > 0) {
                    $usageCount = Order::where('voucher_id', $voucher->id)
                        ->whereNotIn('status', ['cancelled', 'canceled'])
                        ->count();

                    if ($usageCount >= $voucher->limit_penggunaan) {
                        $isValid = false;
                    }
                }

                // c. Cek minimum order
                if ($total < $voucher->minimum_order) {
                    $isValid = false;
                }

                // d. Hitung diskon jika valid
                if ($isValid) {
                    $voucherId = $voucher->id;
                    if ($voucher->diskon_persen) {
                        $discountAmount = $total * ($voucher->diskon_persen / 100);
                    } elseif ($voucher->diskon_nominal) {
                        // ✅ JANGAN BOLEH MELEBIHI SUBTOTAL
                        $discountAmount = min($voucher->diskon_nominal, $total);
                    }

                    // ✅ Batasi maksimum diskon
                    if ($voucher->maksimum_diskon && $discountAmount > $voucher->maksimum_diskon) {
                        $discountAmount = $voucher->maksimum_diskon;
                    }
                }
            }
        }

        // ✅ Total minimal 0
        $finalTotal = max(0, $total - $discountAmount);

        // 3. Buat Order
        $order = Order::create([
            'user_id' => $request->user()->id,
            'jenis_order' => $request->jenis_order,
            'booking_id' => $request->booking_id ?? null,
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

        // ✅ Update penggunaan voucher hanya jika diskon > 0
        if ($voucherId && $discountAmount > 0) {
            Voucher::where('id', $voucherId)->increment('penggunaan_sekarang');
        }

        return response()->json([
            "message" => "Order created successfully",
            "order" => $order->load(['items.menu', 'voucher']),
            "discount" => [
                "applied" => $discountAmount > 0,
                "amount" => (float) $discountAmount,
                "final_total" => (float) $finalTotal
            ]
        ]);
    }

    public function myOrders(Request $request)
    {
        return Order::where('user_id', $request->user()->id)
            ->with(['items.menu', 'voucher'])
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
        
        // ✅ Kembalikan penggunaan voucher jika ada
        if ($order->voucher_id && $order->discount_amount > 0) {
            Voucher::where('id', $order->voucher_id)
                ->where('penggunaan_sekarang', '>', 0)
                ->decrement('penggunaan_sekarang');
        }

        $order->update(['status' => 'cancelled']);
        return $order;
    }

    public function uploadPaymentProof(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'bukti_bayar' => 'required|image|max:2048'
        ]);

        $order = Order::findOrFail($request->order_id);
        
        $path = $request->file('bukti_bayar')->store('payment-proofs', 'public');
        
        $order->update([
            'bukti_bayar' => $path,
            'status' => 'waiting_verification'
        ]);

        return response()->json([
            'message' => 'Payment proof uploaded successfully',
            'order' => $order
        ]);
    }
}