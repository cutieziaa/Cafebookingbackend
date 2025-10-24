<?php
// app/Http/Controllers/MejaController.php
namespace App\Http\Controllers;

use App\Models\Meja;
use App\Models\MejaTipe;
use Illuminate\Http\Request;

class MejaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('cs'); // Hanya CS dan Admin yang bisa kelola meja
    }

    public function index()
    {
        $mejas = Meja::with('tipe')->get();
        $tipeMejas = MejaTipe::all();
        return view('meja.index', compact('mejas', 'tipeMejas'));
    }

    public function create()
    {
        $tipeMejas = MejaTipe::all();
        return view('meja.create', compact('tipeMejas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_id' => 'required|exists:meja_tipe,id',
            'kode_meja' => 'required|string|max:50|unique:meja',
            'kapasitas' => 'required|integer|min:1',
            'status' => 'required|in:aktif,tidak_aktif'
        ]);

        Meja::create($request->all());

        return redirect()->route('meja.index')
            ->with('success', 'Meja berhasil ditambahkan.');
    }

    public function show(Meja $meja)
    {
        $meja->load('tipe');
        return view('meja.show', compact('meja'));
    }

    public function edit(Meja $meja)
    {
        $tipeMejas = MejaTipe::all();
        return view('meja.edit', compact('meja', 'tipeMejas'));
    }

    public function update(Request $request, Meja $meja)
    {
        $request->validate([
            'tipe_id' => 'required|exists:meja_tipe,id',
            'kode_meja' => 'required|string|max:50|unique:meja,kode_meja,' . $meja->id,
            'kapasitas' => 'required|integer|min:1',
            'status' => 'required|in:aktif,tidak_aktif'
        ]);

        $meja->update($request->all());

        return redirect()->route('meja.index')
            ->with('success', 'Meja berhasil diperbarui.');
    }

    public function destroy(Meja $meja)
    {
        $meja->delete();

        return redirect()->route('meja.index')
            ->with('success', 'Meja berhasil dihapus.');
    }
}