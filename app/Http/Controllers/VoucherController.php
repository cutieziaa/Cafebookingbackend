<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::query();
        
        // Filtering
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }
        
        // Sorting
        $sortField = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);
        
        // Pagination atau semua data
        if ($request->has('per_page')) {
            $perPage = $request->get('per_page', 10);
            return $query->paginate($perPage);
        }
        
        return $query->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode' => 'required|unique:voucher|string|max:50',
            'nama' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'tipe_diskon' => 'required|in:persen,nominal',
            'diskon_persen' => 'required_if:tipe_diskon,persen|integer|min:1|max:100',
            'diskon_nominal' => 'required_if:tipe_diskon,nominal|numeric|min:0',
            'maksimum_diskon' => 'nullable|numeric|min:0',
            'minimum_order' => 'required|numeric|min:0',
            'limit_penggunaan' => 'required|integer|min:1',
            'tanggal_mulai' => 'nullable|date',
            'expired_at' => 'nullable|date',
            'hanya_untuk_user_tertentu' => 'boolean',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        
        // Generate kode otomatis jika tidak diisi
        if (empty($data['kode'])) {
            $data['kode'] = Str::upper(Str::random(8));
        }
        
        // Pastikan hanya satu tipe diskon yang diisi
        if ($data['tipe_diskon'] === 'persen') {
            $data['diskon_nominal'] = null;
        } else {
            $data['diskon_persen'] = null;
        }

        $voucher = Voucher::create($data);

        return response()->json([
            'message' => 'Voucher berhasil dibuat',
            'data' => $voucher
        ], 201);
    }

    public function show($id)
    {
        $voucher = Voucher::find($id);
        
        if (!$voucher) {
            return response()->json([
                'message' => 'Voucher tidak ditemukan'
            ], 404);
        }
        
        return response()->json($voucher);
    }

    public function update(Request $request, $id)
    {
        $voucher = Voucher::find($id);
        
        if (!$voucher) {
            return response()->json([
                'message' => 'Voucher tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kode' => 'sometimes|unique:voucher,kode,' . $id,
            'nama' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'tipe_diskon' => 'sometimes|in:persen,nominal',
            'diskon_persen' => 'required_if:tipe_diskon,persen|integer|min:1|max:100',
            'diskon_nominal' => 'required_if:tipe_diskon,nominal|numeric|min:0',
            'maksimum_diskon' => 'nullable|numeric|min:0',
            'minimum_order' => 'sometimes|numeric|min:0',
            'limit_penggunaan' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:aktif,nonaktif,habis',
            'tanggal_mulai' => 'nullable|date',
            'expired_at' => 'nullable|date',
            'hanya_untuk_user_tertentu' => 'boolean',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        
        // Pastikan hanya satu tipe diskon yang diisi
        if (isset($data['tipe_diskon'])) {
            if ($data['tipe_diskon'] === 'persen') {
                $data['diskon_nominal'] = null;
            } else {
                $data['diskon_persen'] = null;
            }
        }

        $voucher->update($data);

        return response()->json([
            'message' => 'Voucher berhasil diperbarui',
            'data' => $voucher
        ]);
    }

    public function destroy($id)
    {
        $voucher = Voucher::find($id);
        
        if (!$voucher) {
            return response()->json([
                'message' => 'Voucher tidak ditemukan'
            ], 404);
        }

        // Cek apakah voucher sudah digunakan
        if ($voucher->penggunaan_sekarang > 0) {
            return response()->json([
                'message' => 'Tidak dapat menghapus voucher yang sudah digunakan'
            ], 400);
        }

        $voucher->delete();

        return response()->json([
            'message' => 'Voucher berhasil dihapus'
        ]);
    }

    public function check(Request $request, $kode)
    {
        $validator = Validator::make(['kode' => $kode], [
            'kode' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode voucher tidak valid'
            ]);
        }

        $userId = $request->user() ? $request->user()->id : null;
        $totalOrder = $request->get('total_order', 0);

        $voucher = Voucher::where('kode', $kode)->first();

        if (!$voucher) {
            return response()->json([
                'valid' => false,
                'message' => 'Voucher tidak ditemukan'
            ]);
        }

        // Cek semua kondisi
        if (!$voucher->isActive()) {
            return response()->json([
                'valid' => false,
                'message' => 'Voucher tidak aktif'
            ]);
        }

        if ($voucher->isExpired()) {
            return response()->json([
                'valid' => false,
                'message' => 'Voucher sudah kedaluwarsa'
            ]);
        }

        if ($voucher->penggunaan_sekarang >= $voucher->limit_penggunaan) {
            return response()->json([
                'valid' => false,
                'message' => 'Voucher sudah habis digunakan'
            ]);
        }

        if ($totalOrder < $voucher->minimum_order) {
            return response()->json([
                'valid' => false,
                'message' => 'Minimum order tidak terpenuhi. Minimum: ' . number_format($voucher->minimum_order, 0, ',', '.')
            ]);
        }

        if ($voucher->hanya_untuk_user_tertentu && $userId) {
            if (!in_array((string)$userId, $voucher->user_ids ?? [])) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Voucher tidak berlaku untuk Anda'
                ]);
            }
        }

        // Hitung diskon
        $diskon = $voucher->hitungDiskon($totalOrder);
        $totalSetelahDiskon = $totalOrder - $diskon;

        return response()->json([
            'valid' => true,
            'message' => 'Voucher valid',
            'voucher' => $voucher,
            'diskon' => $diskon,
            'total_setelah_diskon' => $totalSetelahDiskon
        ]);
    }

    // API untuk generate kode voucher otomatis
    public function generateCode()
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (Voucher::where('kode', $code)->exists());

        return response()->json([
            'kode' => $code
        ]);
    }
    // API untuk statistik voucher
    public function statistics()
    {
        $total = Voucher::count();
        $aktif = Voucher::where('status', 'aktif')->count();
        $habis = Voucher::where('status', 'habis')->count();
        $expired = Voucher::where('expired_at', '<', now())->count();

        return response()->json([
            'total' => $total,
            'aktif' => $aktif,
            'habis' => $habis,
            'expired' => $expired,
            'penggunaan_hari_ini' => 0, // Bisa diisi dengan logika sesuai kebutuhan
        ]);
    }
}