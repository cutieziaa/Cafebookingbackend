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
}
