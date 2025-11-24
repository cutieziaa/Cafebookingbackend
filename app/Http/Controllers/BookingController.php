<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        return Booking::with(['user', 'meja'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'meja_id' => 'required',
            'tanggal' => 'required',
            'jumlah_orang' => 'required|integer'
        ]);

        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'meja_id' => $request->meja_id,
            'tanggal' => $request->tanggal,
            'jumlah_orang' => $request->jumlah_orang,
            'status' => 'pending'
        ]);

        return $booking;
    }

    public function myBooking(Request $request)
    {
        return Booking::with('meja')
            ->where('user_id', $request->user()->id)
            ->get();
    }
}
