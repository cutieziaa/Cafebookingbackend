<?php

namespace App\Http\Controllers; // <-- PASTIKAN NAMESPACE INI BENAR

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Meja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingController extends Controller
{
    // Get all bookings with filtering
    public function index(Request $request)
    {
        try {
            $query = Booking::with(['user', 'meja']);

            // Filter by status
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            // Filter by date
            if ($request->has('tanggal')) {
                $query->whereDate('tanggal', $request->tanggal);
            }

            // Filter by meja_id
            if ($request->has('meja_id')) {
                $query->where('meja_id', $request->meja_id);
            }

            // Sort by date (latest first)
            $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');

            // Pagination
            $perPage = $request->get('per_page', 10);
            $bookings = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $bookings->items(),
                'meta' => [
                    'current_page' => $bookings->currentPage(),
                    'per_page' => $bookings->perPage(),
                    'total' => $bookings->total(),
                    'last_page' => $bookings->lastPage()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching bookings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get user's own bookings
    public function myBookings(Request $request)
    {
        try {
            $user = $request->user();

            $bookings = Booking::with(['meja', 'order'])
                ->where('user_id', $user->id)
                ->orderBy('tanggal', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $bookings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching user bookings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Create new booking
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'meja_id' => 'required|exists:meja,id',
                'tanggal' => 'required|date|after_or_equal:today',
                'waktu_selesai' => 'required|date|after:tanggal',
                'jumlah_orang' => 'nullable|integer|min:1',
                'catatan' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if table is available
            $meja = Meja::find($request->meja_id);
            if (!$meja->tersedia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Meja tidak tersedia untuk dipesan'
                ], 400);
            }

            // Check for overlapping bookings
            $overlap = Booking::where('meja_id', $request->meja_id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($request) {
                    $query->whereBetween('tanggal', [$request->tanggal, $request->waktu_selesai])
                        ->orWhereBetween('waktu_selesai', [$request->tanggal, $request->waktu_selesai])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('tanggal', '<=', $request->tanggal)
                                ->where('waktu_selesai', '>=', $request->waktu_selesai);
                        });
                })
                ->exists();

            if ($overlap) {
                return response()->json([
                    'success' => false,
                    'message' => 'Meja sudah dipesan pada waktu tersebut'
                ], 400);
            }
            $jumlahOrang = $request->jumlah_orang ?? 1;

            // Create booking
            $booking = Booking::create([
                'user_id' => $request->user()->id,
                'meja_id' => $request->meja_id,
                'kode_booking' => 'BKJ-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6)),
                'tanggal' => $request->tanggal,
                'waktu_selesai' => $request->waktu_selesai,
                'jumlah_orang' => $jumlahOrang,
                'catatan' => $request->catatan,
                'status' => 'pending'
            ]);

            // Update table status
            $meja->tersedia = false;
            $meja->save();

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil dibuat',
                'data' => $booking->load(['user', 'meja'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get booking detail
    public function show($id)
    {
        try {
            $booking = Booking::with(['user', 'meja', 'order'])->find($id);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $booking
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update booking status (for admin)
    public function updateStatus(Request $request, $id)
    {
        try {
            $booking = Booking::find($id);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,confirmed,cancelled'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update booking status
            $oldStatus = $booking->status;
            $booking->status = $request->status;
            $booking->save();

            // Update table status if booking is cancelled
            if ($request->status === 'cancelled' && $oldStatus !== 'cancelled') {
                $meja = $booking->meja;
                $meja->tersedia = '1';
                $meja->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Status booking berhasil diupdate',
                'data' => $booking
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating booking status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Cancel booking (for user)
    public function cancel(Request $request, $id)
    {
        try {
            $booking = Booking::find($id);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking tidak ditemukan'
                ], 404);
            }

            // Check if user owns the booking
            if ($booking->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk membatalkan booking ini'
                ], 403);
            }

            // Check if booking can be cancelled
            if ($booking->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking sudah dibatalkan'
                ], 400);
            }

            if ($booking->status === 'confirmed') {
                $bookingTime = Carbon::parse($booking->tanggal);
                if ($bookingTime->diffInHours(now()) < 2) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Booking yang sudah dikonfirmasi hanya bisa dibatalkan minimal 2 jam sebelum waktu booking'
                    ], 400);
                }
            }

            // Update booking status
            $booking->status = 'cancelled';
            $booking->save();

            // Update table status
            $meja = $booking->meja;
            $meja->status = 'tersedia';
            $meja->save();

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil dibatalkan',
                'data' => $booking
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get available tables for booking
    public function availableTables(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tanggal' => 'required|date|after_or_equal:today',
                'waktu_selesai' => 'required|date|after:tanggal',
                'jumlah_orang' => 'nullable|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get all available tables
            $query = Meja::where('status', 'tersedia');

            if ($request->has('jumlah_orang') && $request->jumlah_orang > 0) {
                $query->where('kapasitas', '>=', $request->jumlah_orang);
            }

            $availableTables = $query->whereDoesntHave('bookings', function ($query) use ($request) {
                $query->where('status', '!=', 'cancelled')
                    ->where(function ($q) use ($request) {
                        $q->whereBetween('tanggal', [$request->tanggal, $request->waktu_selesai])
                            ->orWhereBetween('waktu_selesai', [$request->tanggal, $request->waktu_selesai])
                            ->orWhere(function ($sub) use ($request) {
                                $sub->where('tanggal', '<=', $request->tanggal)
                                    ->where('waktu_selesai', '>=', $request->waktu_selesai);
                            });
                    });
            })
                ->orderBy('kapasitas')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $availableTables
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching available tables',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get booking statistics
    public function statistics()
    {
        try {
            $total = Booking::count();
            $pending = Booking::where('status', 'pending')->count();
            $confirmed = Booking::where('status', 'confirmed')->count();
            $cancelled = Booking::where('status', 'cancelled')->count();

            // Today's bookings
            $today = Booking::whereDate('tanggal', today())->count();

            // This week's bookings
            $weekStart = now()->startOfWeek();
            $weekEnd = now()->endOfWeek();
            $thisWeek = Booking::whereBetween('tanggal', [$weekStart, $weekEnd])->count();

            return response()->json([
                'success' => true,
                'total' => $total,
                'pending' => $pending,
                'confirmed' => $confirmed,
                'cancelled' => $cancelled,
                'today' => $today,
                'this_week' => $thisWeek
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching booking statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}