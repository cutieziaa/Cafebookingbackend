<?php
// app/Http/Controllers/MenuController.php
namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function __construct()
    {
        // Hanya ADMIN yang boleh CRUD (create, edit, update, destroy)
       $this->middleware('auth');
       $this->middleware('admin')->except(['index', 'show']);
    }

    public function index()
    {
        $menus = Menu::all();
        return view('menu.index', compact('menus'));
    }

    public function create()
    {
         return view('menu.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'nama' => $request->nama,
            'harga' => $request->harga,
            'deskripsi' => $request->deskripsi,
            'tersedia' => $request->has('tersedia'),
        ];

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('menu-images', 'public');
            $data['gambar_url'] = $path;
        }

        Menu::create($data);

        return redirect()->route('menu.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    public function show(Menu $menu)
    {
        return view('menu.show', compact(var_name: 'menu'));
    }

    public function edit(Menu $menu)
    {
        return view('menu.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'nama' => $request->nama,
            'harga' => $request->harga,
            'deskripsi' => $request->deskripsi,
            'tersedia' => $request->has('tersedia'),
        ];

        if ($request->hasFile('gambar')) {
            if ($menu->gambar_url) {
                Storage::disk('public')->delete($menu->gambar_url);
            }
            $path = $request->file('gambar')->store('menu-images', 'public');
            $data['gambar_url'] = $path;
        }

        $menu->update($data);

        return redirect()->route('menu.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu)
    {
        if ($menu->gambar_url) {
            Storage::disk('public')->delete($menu->gambar_url);
        }

        $menu->delete();

        return redirect()->route('menu.index')
            ->with('success', 'Menu berhasil dihapus.');
    }
}
