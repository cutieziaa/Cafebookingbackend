<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\OrderPickup; // Pastikan model ini ada
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $data = [];

        if ($user->isAdmin()) {
            $data = [
                'total_users' => User::count(),
                'total_meja' => Meja::where('status', 'aktif')->count(),
                'total_menu' => Menu::where('tersedia', true)->count(),
                'booking_menunggu' => Booking::menunggu()->count(),
                'recent_bookings' => Booking::with(['user', 'meja'])->latest()->take(5)->get(),
            ];
            
            // Tambahkan orders jika ada model OrderPickup
            if (class_exists(OrderPickup::class)) {
                $data['order_baru'] = OrderPickup::where('order_status', 'baru')->count();
                $data['recent_orders'] = OrderPickup::with(['user'])->latest()->take(5)->get();
            }
            
        } elseif ($user->isCs()) {
            $data = [
                'booking_menunggu' => Booking::menunggu()->count(),
                'booking_dikonfirmasi' => Booking::dikonfirmasi()->count(),
                'recent_bookings' => Booking::with(['user', 'meja'])->latest()->take(10)->get(),
            ];
            
            // Tambahkan orders jika ada model OrderPickup
            if (class_exists(OrderPickup::class)) {
                $data['order_baru'] = OrderPickup::where('order_status', 'baru')->count();
                $data['recent_orders'] = OrderPickup::with(['user'])->latest()->take(10)->get();
            }
            
        } else {
            // Data untuk customer
            $data = [
                'my_bookings' => Booking::where('pengguna_id', $user->id)->latest()->get(),
                'active_bookings' => Booking::where('pengguna_id', $user->id)
                    ->whereIn('booking_status', ['menunggu', 'dikonfirmasi'])->get()
            ];
            
            // Tambahkan orders jika ada model OrderPickup
            if (class_exists(OrderPickup::class)) {
                $data['my_orders'] = OrderPickup::where('pengguna_id', $user->id)->latest()->get();
            } else {
                $data['my_orders'] = collect(); // Empty collection jika model tidak ada
            }
        }

        return view('dashboard', compact('data'));
    }
}