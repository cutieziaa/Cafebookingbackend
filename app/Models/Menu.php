<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';

    protected $fillable = [
        'nama', 'harga', 'deskripsi',
        'gambar_url', 'tersedia'
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'tersedia' => 'boolean'
    ];
    
    // Accessor untuk URL gambar lengkap
    public function getGambarUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        // Jika sudah URL lengkap, return langsung
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        
        // Jika hanya path, tambahkan base URL
        return config('app.url') . '/storage/' . $value;
    }
}