<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Tambahkan ini
use Illuminate\Database\QueryException; // Tambahkan ini

class BookingController extends Controller
{
    // ... method index dan myBooking tetap sama ...

    public function store(Request $request)
    {
        // Kita abaikan semua input dari user dan paksa simpan data
        $booking = new Booking();
        $booking->user_id = $request->user()->id; // Ambil user yang login
        $booking->meja_id = 1; // ID meja statis
        $booking->tanggal = now()->addDay()->setTime(19, 0);
        $booking->waktu_selesai = now()->addDay()->setTime(21, 0);
        $booking->jumlah_orang = 2;
        $booking->status = 'pending';

        $booking->save();

        return response()->json(['message' => 'Booking berhasil disimpan!', 'data' => $booking]);
    }

}