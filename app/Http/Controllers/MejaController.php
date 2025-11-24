<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use Illuminate\Http\Request;

class MejaController extends Controller
{
    public function index()
    {
        return Meja::with('tipe')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'meja_tipe_id' => 'required',
            'nomor' => 'required|integer',
        ]);

        return Meja::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $meja = Meja::findOrFail($id);
        $meja->update($request->all());
        return $meja;
    }

    public function destroy($id)
    {
        Meja::destroy($id);

        return response()->json(['message' => 'Meja dihapus']);
    }
}
