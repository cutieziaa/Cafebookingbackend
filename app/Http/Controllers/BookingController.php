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
        // 1. Perbarui validasi untuk menambahkan 'waktu_selesai'
        $request->validate([
            'meja_id' => 'required|exists:meja,id',
            'tanggal' => 'required|date|after_or_equal:now',
            'waktu_selesai' => 'required|date|after:tanggal', // Waktu selesai harus setelah waktu mulai
            'jumlah_orang' => 'required|integer|min:1'
        ]);

        // 2. Tambahkan 'waktu_selesai' saat membuat booking
        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'meja_id' => $request->meja_id,
            'tanggal' => $request->tanggal,
            'waktu_selesai' => $request->waktu_selesai, // <-- TAMBAHKAN INI
            'jumlah_orang' => $request->jumlah_orang,
            'status' => 'pending'
        ]);

        return response()->json($booking, 201);
    }

    public function myBooking(Request $request)
    {
        return Booking::with('meja')
            ->where('user_id', $request->user()->id)
            ->get();
    }

    
}