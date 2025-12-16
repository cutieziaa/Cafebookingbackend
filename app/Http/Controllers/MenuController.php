<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    public function index()
    {
        return Menu::all();
    }

    public function show($id)
    {
        return Menu::findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'gambar_url' => 'nullable|string|url',
            'tersedia' => 'boolean'
        ]);

        return Menu::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        
        $request->validate([
            'nama' => 'sometimes|required|string|max:100',
            'harga' => 'sometimes|required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'gambar_url' => 'nullable|string|url',
            'tersedia' => 'boolean'
        ]);

        $menu->update($request->all());
        return $menu;
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        
        // Hapus file gambar jika ada
        if ($menu->gambar_url) {
            $this->deleteImageFile($menu->gambar_url);
        }
        
        $menu->delete();
        return response()->json(['message' => 'Menu dihapus']);
    }

    // ==================== ENDPOINT UPLOAD GAMBAR ====================
    public function upload(Request $request)
    {
        // Validasi file
        $validator = Validator::make($request->all(), [
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048' // 2MB
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Generate nama file unik
            $fileName = time() . '_' . uniqid() . '.' . $request->file('gambar')->getClientOriginalExtension();
            
            // Simpan file ke storage
            $path = $request->file('gambar')->storeAs('menu_images', $fileName, 'public');
            
            // Generate URL lengkap
            $baseUrl = config('app.url');
            $url = $baseUrl . '/storage/' . $path;
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $path,
                'filename' => $fileName
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload gambar: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // ==================== ENDPOINT DELETE GAMBAR ====================
    public function deleteImage($id)
    {
        try {
            $menu = Menu::findOrFail($id);
            
            if (!$menu->gambar_url) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu tidak memiliki gambar'
                ], 404);
            }
            
            // Hapus file dari storage
            $this->deleteImageFile($menu->gambar_url);
            
            // Update database
            $menu->update(['gambar_url' => null]);
            
            return response()->json([
                'success' => true,
                'message' => 'Gambar berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus gambar: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // ==================== HELPER FUNCTION ====================
    private function deleteImageFile($imageUrl)
    {
        try {
            // Ekstrak path dari URL
            $parsedUrl = parse_url($imageUrl);
            $path = $parsedUrl['path'] ?? '';
            
            // Hapus '/storage/' dari awal path
            if (strpos($path, '/storage/') === 0) {
                $relativePath = substr($path, 9); // Hapus '/storage/'
                
                // Hapus file dari storage
                if (Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->delete($relativePath);
                }
            }
        } catch (\Exception $e) {
            // Log error tapi jangan gagalkan operasi utama
            \Log::error('Gagal menghapus file gambar: ' . $e->getMessage());
        }
    }
}