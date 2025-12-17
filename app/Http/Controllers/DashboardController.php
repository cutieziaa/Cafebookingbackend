<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Pickup;
use App\Models\Voucher;
use App\Models\Booking;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get user dashboard summary
     */
    public function dashboardSummary(Request $request)
    {
        $user = Auth::user();
        
        try {
            // Total orders
            $totalOrders = Order::where('user_id', $user->id)->count();
            
            // Total spent (only paid orders)
            $totalSpent = Order::where('user_id', $user->id)
                ->where('status', 'paid')
                ->sum('total');
            
            // Count today's orders
            $todayOrders = Order::where('user_id', $user->id)
                ->whereDate('created_at', Carbon::today())
                ->count();
            
            // Pending orders
            $pendingOrders = Order::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count();
            
            // Active vouchers for this user
            $activeVouchers = Voucher::where('status', 'aktif')
                ->where('expired_at', '>', now())
                ->where('penggunaan_sekarang', '<', DB::raw('limit_penggunaan'))
                ->where(function ($query) use ($user) {
                    $query->where('hanya_untuk_user_tertentu', 0)
                        ->orWhere(function ($q) use ($user) {
                            $q->where('hanya_untuk_user_tertentu', 1)
                                ->where('user_ids', 'like', '%"' . $user->id . '"%')
                                ->orWhere('user_ids', 'like', '%' . $user->id . '%');
                        });
                })
                ->count();
            
            // Upcoming bookings
            $upcomingBookings = Booking::where('user_id', $user->id)
                ->whereDate('tanggal', '>=', Carbon::today())
                ->whereIn('status', ['confirmed', 'pending'])
                ->count();
            
            // Recent orders (last 3)
            $recentOrders = Order::with(['voucher', 'booking'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                        'total' => (float) $order->total,
                        'discount_amount' => (float) $order->discount_amount,
                        'status' => $order->status,
                        'jenis_order' => $order->jenis_order,
                        'created_at' => $order->created_at,
                        'items_count' => OrderItem::where('order_id', $order->id)->count()
                    ];
                });
            
            // Recent pickups (last 3)
            $recentPickups = Pickup::with(['order', 'user'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($pickup) {
                    return [
                        'id' => $pickup->id,
                        'nama_penexima' => $pickup->nama_penexima,
                        'status' => $pickup->status,
                        'catatan' => $pickup->catatan,
                        'created_at' => $pickup->created_at,
                        'order' => $pickup->order ? [
                            'total' => (float) $pickup->order->total,
                            'discount_amount' => (float) $pickup->order->discount_amount
                        ] : null
                    ];
                });
            
            // Active vouchers list
            $vouchers = Voucher::where('status', 'aktif')
                ->where('expired_at', '>', now())
                ->where('penggunaan_sekarang', '<', DB::raw('limit_penggunaan'))
                ->where(function ($query) use ($user) {
                    $query->where('hanya_untuk_user_tertentu', 0)
                        ->orWhere(function ($q) use ($user) {
                            $q->where('hanya_untuk_user_tertentu', 1)
                                ->where('user_ids', 'like', '%"' . $user->id . '"%')
                                ->orWhere('user_ids', 'like', '%' . $user->id . '%');
                        });
                })
                ->orderBy('expired_at', 'asc')
                ->limit(3)
                ->get()
                ->map(function ($voucher) {
                    return [
                        'id' => $voucher->id,
                        'kode' => $voucher->kode,
                        'nama' => $voucher->nama,
                        'tipe_diskon' => $voucher->tipe_diskon,
                        'diskon_persen' => $voucher->diskon_persen,
                        'diskon_nominal' => (float) $voucher->diskon_nominal,
                        'minimum_order' => (float) $voucher->minimum_order,
                        'expired_at' => $voucher->expired_at,
                        'limit_penggunaan' => $voucher->limit_penggunaan,
                        'penggunaan_sekarang' => $voucher->penggunaan_sekarang
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => [
                        'total_orders' => $totalOrders,
                        'total_spent' => (float) $totalSpent,
                        'today_orders' => $todayOrders,
                        'pending_orders' => $pendingOrders,
                        'active_vouchers' => $activeVouchers,
                        'upcoming_bookings' => $upcomingBookings,
                    ],
                    'recent_orders' => $recentOrders,
                    'recent_pickups' => $recentPickups,
                    'active_vouchers_list' => $vouchers,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get user order history with filters
     */
    public function orderHistory(Request $request)
    {
        $user = Auth::user();
        
        $query = Order::with(['voucher', 'booking', 'orderItems.menu'])
            ->where('user_id', $user->id);
        
        // Apply filters
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('jenis_order') && $request->jenis_order != 'all') {
            $query->where('jenis_order', $request->jenis_order);
        }
        
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date
            ]);
        }
        
        $orders = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);
        
        // Format the response
        $formattedOrders = $orders->getCollection()->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                'total' => (float) $order->total,
                'discount_amount' => (float) $order->discount_amount,
                'jenis_order' => $order->jenis_order,
                'status' => $order->status,
                'voucher' => $order->voucher ? [
                    'kode' => $order->voucher->kode,
                    'nama' => $order->voucher->nama
                ] : null,
                'items' => $order->orderItems->map(function ($item) {
                    return [
                        'menu_name' => $item->menu->nama ?? 'Unknown Item',
                        'quantity' => $item->qty,
                        'price' => (float) $item->harga,
                        'subtotal' => (float) $item->qty * (float) $item->harga
                    ];
                }),
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => [
                'current_page' => $orders->currentPage(),
                'data' => $formattedOrders,
                'first_page_url' => $orders->url(1),
                'from' => $orders->firstItem(),
                'last_page' => $orders->lastPage(),
                'last_page_url' => $orders->url($orders->lastPage()),
                'links' => $orders->linkCollection()->toArray(),
                'next_page_url' => $orders->nextPageUrl(),
                'path' => $orders->path(),
                'per_page' => $orders->perPage(),
                'prev_page_url' => $orders->previousPageUrl(),
                'to' => $orders->lastItem(),
                'total' => $orders->total(),
            ]
        ]);
    }
    
    /**
     * Get user pickup history
     */
    public function pickupHistory(Request $request)
    {
        $user = Auth::user();
        
        $query = Pickup::with(['order', 'order.voucher', 'order.orderItems.menu'])
            ->where('user_id', $user->id);
        
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        $pickups = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);
        
        // Format the response
        $formattedPickups = $pickups->getCollection()->map(function ($pickup) {
            return [
                'id' => $pickup->id,
                'nama_penexima' => $pickup->nama_penexima,
                'catatan' => $pickup->catatan,
                'status' => $pickup->status,
                'order' => $pickup->order ? [
                    'id' => $pickup->order->id,
                    'total' => (float) $pickup->order->total,
                    'discount_amount' => (float) $pickup->order->discount_amount,
                    'jenis_order' => 'pickup',
                    'voucher' => $pickup->order->voucher ? [
                        'kode' => $pickup->order->voucher->kode,
                        'nama' => $pickup->order->voucher->nama
                    ] : null,
                    'items' => $pickup->order->orderItems->map(function ($item) {
                        return [
                            'menu_name' => $item->menu->nama ?? 'Unknown Item',
                            'quantity' => $item->qty,
                            'price' => (float) $item->harga,
                            'subtotal' => (float) $item->qty * (float) $item->harga
                        ];
                    })
                ] : null,
                'created_at' => $pickup->created_at,
                'updated_at' => $pickup->updated_at
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => [
                'current_page' => $pickups->currentPage(),
                'data' => $formattedPickups,
                'first_page_url' => $pickups->url(1),
                'from' => $pickups->firstItem(),
                'last_page' => $pickups->lastPage(),
                'last_page_url' => $pickups->url($pickups->lastPage()),
                'links' => $pickups->linkCollection()->toArray(),
                'next_page_url' => $pickups->nextPageUrl(),
                'path' => $pickups->path(),
                'per_page' => $pickups->perPage(),
                'prev_page_url' => $pickups->previousPageUrl(),
                'to' => $pickups->lastItem(),
                'total' => $pickups->total(),
            ]
        ]);
    }
    
    /**
     * Get user voucher history
     */
    public function voucherHistory(Request $request)
    {
        $user = Auth::user();
        
        // Vouchers used by user
        $query = Order::with(['voucher'])
            ->where('user_id', $user->id)
            ->whereNotNull('voucher_id');
        
        if ($request->has('status') && $request->status != 'all') {
            $query->whereHas('voucher', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }
        
        $voucherUsage = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);
        
        // Format the response
        $formattedUsage = $voucherUsage->getCollection()->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                'total' => (float) $order->total,
                'discount_amount' => (float) $order->discount_amount,
                'created_at' => $order->created_at,
                'voucher' => $order->voucher ? [
                    'id' => $order->voucher->id,
                    'kode' => $order->voucher->kode,
                    'nama' => $order->voucher->nama,
                    'tipe_diskon' => $order->voucher->tipe_diskon,
                    'diskon_persen' => $order->voucher->diskon_persen,
                    'diskon_nominal' => (float) $order->voucher->diskon_nominal,
                    'status' => $order->voucher->status
                ] : null
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => [
                'current_page' => $voucherUsage->currentPage(),
                'data' => $formattedUsage,
                'first_page_url' => $voucherUsage->url(1),
                'from' => $voucherUsage->firstItem(),
                'last_page' => $voucherUsage->lastPage(),
                'last_page_url' => $voucherUsage->url($voucherUsage->lastPage()),
                'links' => $voucherUsage->linkCollection()->toArray(),
                'next_page_url' => $voucherUsage->nextPageUrl(),
                'path' => $voucherUsage->path(),
                'per_page' => $voucherUsage->perPage(),
                'prev_page_url' => $voucherUsage->previousPageUrl(),
                'to' => $voucherUsage->lastItem(),
                'total' => $voucherUsage->total(),
            ]
        ]);
    }
    
    /**
     * Get user booking history
     */
    public function bookingHistory(Request $request)
    {
        $user = Auth::user();
        
        $query = Booking::with(['meja', 'meja.tipe'])
            ->where('user_id', $user->id);
        
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal', [
                $request->start_date,
                $request->end_date
            ]);
        }
        
        $bookings = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai', 'desc')
            ->paginate($request->per_page ?? 10);
        
        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }
}