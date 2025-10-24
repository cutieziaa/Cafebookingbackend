<?php
// app/Http/Controllers/BookingController.php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Meja;
use App\Models\MejaTipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin() || $user->isCs()) {
            $bookings = Booking::with(['user', 'meja.tipe'])->latest()->get();
        } else {
            $bookings = Booking::with(['user', 'meja.tipe'])
                ->where('pengguna_id', $user->id)
                ->latest()
                ->get();
        }

        return view('booking.index', compact('bookings'));
    }

    public function create()
    {
        $tipeMeja = MejaTipe::with(['meja' => function($query) {
            $query->where('status', 'aktif');
        }])->get();

        return view('booking.create', compact('tipeMeja'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_id' => 'required|exists:meja_tipe,id',
            'meja_id' => 'required|exists:meja,id',
            'tanggal_booking' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required',
            'durasi_minutes' => 'required|integer|min:30|max:240',
            'jumlah_orang' => 'required|integer|min:1'
        ]);

        $meja = Meja::findOrFail($request->meja_id);

        // Cek ketersediaan meja
        if (!$meja->isAvailable($request->tanggal_booking, $request->waktu_mulai, $request->durasi_minutes)) {
            return redirect()->back()
                ->with('error', 'Meja tidak tersedia pada waktu yang dipilih.')
                ->withInput();
        }

        // Hitung total amount (contoh: Rp 50,000 per jam)
        $total_amount = ($request->durasi_minutes / 60) * 50000;

        Booking::create([
            'pengguna_id' => Auth::id(),
            'meja_id' => $request->meja_id,
            'tanggal_booking' => $request->tanggal_booking,
            'waktu_mulai' => $request->waktu_mulai,
            'durasi_minutes' => $request->durasi_minutes,
            'jumlah_orang' => $request->jumlah_orang,
            'total_amount' => $total_amount,
            'booking_status' => 'menunggu'
        ]);

        return redirect()->route('booking.index')
            ->with('success', 'Booking berhasil dibuat. Menunggu konfirmasi.');
    }

    public function show(Booking $booking)
    {
        // Authorization
        if (Auth::user()->isCustomer() && $booking->pengguna_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['user', 'meja.tipe', 'assignedBy']);

        return view('booking.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isCs()) {
            abort(403);
        }

        $request->validate([
            'booking_status' => 'required|in:dikonfirmasi,batal,selesai'
        ]);

        $booking->update([
            'booking_status' => $request->booking_status,
            'assigned_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Status booking berhasil diperbarui.');
    }

    public function destroy(Booking $booking)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isCs() && $booking->pengguna_id !== Auth::id()) {
            abort(403);
        }

        // Hanya bisa hapus jika status masih menunggu
        if ($booking->booking_status !== 'menunggu' && Auth::user()->isCustomer()) {
            return redirect()->back()->with('error', 'Hanya bisa membatalkan booking dengan status menunggu.');
        }

        $booking->delete();

        return redirect()->route('booking.index')
            ->with('success', 'Booking berhasil dihapus.');
    }

     public function getAvailableMeja(Request $request)
    {
        $request->validate([
            'tipe_id' => 'required|exists:meja_tipe,id',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'durasi' => 'required|integer',
            'jumlah_orang' => 'required|integer'
        ]);

        $availableMeja = Meja::where('tipe_id', $request->tipe_id)
            ->where('status', 'aktif')
            ->where('kapasitas', '>=', $request->jumlah_orang)
            ->get()
            ->filter(function($meja) use ($request) {
                return $meja->isAvailable($request->tanggal, $request->waktu_mulai, $request->durasi);
            })
            ->values(); // Reset keys

        return response()->json($availableMeja);
    }
}