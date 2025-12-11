<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Tambahkan ini
use Illuminate\Validation\ValidationException; // Tambahkan ini

class BookingController extends Controller
{
    // ... method lainnya ...

    public function store(Request $request)
    {
        // --- LOG DEBUG 1: Lihat semua data yang diterima ---
        Log::info('Data request yang diterima:', $request->all());
        Log::info('Header Content-Type: ' . $request->header('Content-Type'));
        // ----------------------------------------------------

        // --- LOG DEBUG 2: Jalankan validasi dan tangkap errornya ---
        $validator = \Validator::make($request->all(), [
            'meja_id' => 'required|exists:meja,id',
            'tanggal' => 'required|date|after_or_equal:now',
            'waktu_selesai' => 'required|date|after:tanggal',
            'jumlah_orang' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            Log::error('Validasi gagal: ' . $validator->errors()->first());
            // Kembalikan error validasi agar bisa dilihat di Postman
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }
        // ---------------------------------------------------------

        // Jika sampai sini, berarti validasi lolos
        Log::info('Validasi berhasil, mencoba menyimpan booking...');

        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'meja_id' => $request->meja_id,
            'tanggal' => $request->tanggal,
            'waktu_selesai' => $request->waktu_selesai,
            'jumlah_orang' => $request->jumlah_orang,
            'status' => 'pending'
        ]);

        Log::info('Booking berhasil dibuat dengan ID: ' . $booking->id);

        return response()->json($booking, 201);
    }
}