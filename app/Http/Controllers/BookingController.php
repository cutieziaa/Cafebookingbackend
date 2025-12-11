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
        // 1. Validasi
        $request->validate([
            'meja_id' => 'required|exists:meja,id',
            'tanggal' => 'required|date|after_or_equal:now',
            'waktu_selesai' => 'required|date|after:tanggal',
            'jumlah_orang' => 'required|integer|min:1'
        ]);

        try {
            // 2. Buat booking
            $booking = Booking::create([
                'user_id' => $request->user()->id,
                'meja_id' => $request->meja_id,
                'tanggal' => $request->tanggal,
                'waktu_selesai' => $request->waktu_selesai,
                'jumlah_orang' => $request->jumlah_orang,
                'status' => 'pending'
            ]);

            // 3. Cek apakah benar-benar berhasil dibuat
            if ($booking->wasRecentlyCreated) {
                Log::info('Booking berhasil dibuat dengan ID: ' . $booking->id);
                return response()->json($booking, 201);
            } else {
                // Kasus yang jarang terjadi, tapi mungkin terjadi jika ada event listener
                Log::error('Booking::create() dipanggil tetapi $booking->wasRecentlyCreated adalah false.');
                return response()->json(['message' => 'Gagal membuat booking, tidak ada perubahan.'], 400);
            }

        } catch (QueryException $e) {
            // Tangkap error spesifik dari database
            Log::error('Gagal menyimpan booking karena error database: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal menyimpan booking. Error database.', 'error' => $e->getMessage()], 500);

        } catch (\Exception $e) {
            // Tangkap error umum lainnya
            Log::error('Terjadi error saat membuat booking: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan server.', 'error' => $e->getMessage()], 500);
        }
    }
}