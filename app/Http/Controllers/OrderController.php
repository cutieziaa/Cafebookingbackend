<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Voucher; // Import model Voucher
use Illuminate\Http\Request;
use Carbon\Carbon; // Import Carbon untuk pengecekan tanggal

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
            'items.*.qty' => 'required|integer|min:1',
            // Tambahkan validasi untuk voucher kode (opsional)
            'voucher_code' => 'sometimes|string|exists:voucher,kode'
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
            $harga = $menu->harga;
            $qty = $i['qty'];
            $subtotal = $harga * $qty;

            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $menu->id,
                'qty' => $qty,
                'harga' => $harga
            ]);

            $total += $subtotal;
        }

        // --- AWAL LOGIKA VOUCHER ---
        $discountAmount = 0;
        $voucherId = null;

        if ($request->filled('voucher_code')) {
            $voucher = Voucher::where('kode', $request->voucher_code)->first();

            if ($voucher) {
                // Validasi Voucher
                $isValid = true;

                // 1. Cek apakah sudah kadaluarsa
                if ($voucher->expired_at && Carbon::now()->isAfter($voucher->expired_at)) {
                    $isValid = false;
                }

                // 2. Cek limit penggunaan
                if ($voucher->limit_penggunaan > 0) {
                    $usageCount = Order::where('voucher_id', $voucher->id)->count();
                    if ($usageCount >= $voucher->limit_penggunaan) {
                        $isValid = false;
                    }
                }

                // 3. Cek minimum order
                if ($total < $voucher->minimum_order) {
                    $isValid = false;
                }

                // Jika voucher valid, hitung diskon
                if ($isValid) {
                    $voucherId = $voucher->id;
                    if ($voucher->diskon_persen) {
                        // Jika diskon persen, hitung berdasarkan total
                        $discountAmount = $total * ($voucher->diskon_persen / 100);
                    } elseif ($voucher->diskon_nominal) {
                        // Jika diskon nominal, gunakan nilai langsung
                        $discountAmount = $voucher->diskon_nominal;
                    }
                }
            }
        }
        // --- AKHIR LOGIKA VOUCHER ---

        $finalTotal = $total - $discountAmount;

        // 3. Update total order beserta info voucher
        $order->update([
            'total' => $finalTotal,
            'voucher_id' => $voucherId,
            'discount_amount' => $discountAmount,
        ]);

        return response()->json([
            "message" => "Order created successfully",
            "order" => $order->load('items.menu', 'voucher') // Load relasi voucher juga
        ]);
    }


    public function myOrders(Request $request)
    {
        // Tambahkan 'voucher' pada with() agar data voucher muncul
        return Order::where('user_id', $request->user()->id)
            ->with('items', 'voucher')
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

    public function uploadPaymentProof(Request $request)
    {
    $request->validate([
        'order_id' => 'required|exists:orders,id',
        'bukti_bayar' => 'required|image|max:2048'
    ]);

    $order = Order::findOrFail($request->order_id);
    
    // Simpan gambar
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