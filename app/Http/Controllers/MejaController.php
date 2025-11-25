<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use Illuminate\Http\Request;

class MejaController extends Controller
{
    // GET /meja
    public function index()
    {
        $data = Meja::with('tipe')->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // POST /meja
    public function store(Request $request)
    {
        $validated = $request->validate([
            'meja_tipe_id' => 'required|exists:meja_tipe,id',
            'nomor'        => ['required', 'string', 'max:10', 'regex:/^[A-Z][0-9]{2}$/'], 
        ]);

        $meja = Meja::create([
            'meja_tipe_id' => $validated['meja_tipe_id'],
            'nomor'        => $validated['nomor'],
            'tersedia'     => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Meja berhasil dibuat',
            'data' => $meja
        ], 201);
    }

    // PUT /meja/{id}
    public function update(Request $request, $id)
    {
        $meja = Meja::findOrFail($id);

        $validated = $request->validate([
            'meja_tipe_id' => 'sometimes|exists:meja_tipe,id',
            'nomor'        => ['sometimes', 'string', 'max:10', 'regex:/^[A-Z][0-9]{2}$/'],
            'tersedia'     => 'sometimes|boolean',
        ]);

        $meja->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Meja berhasil diperbarui',
            'data' => $meja
        ]);
    }

    // DELETE /meja/{id}
    public function destroy($id)
    {
        $meja = Meja::findOrFail($id);
        $meja->delete();

        return response()->json([
            'success' => true,
            'message' => 'Meja berhasil dihapus'
        ]);
    }
}
