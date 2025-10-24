<?php
// app/Models/Menu.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';
    protected $fillable = ['nama', 'harga', 'deskripsi', 'gambar_url', 'tersedia'];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'tersedia' => 'boolean'
        ];
    }

    public function getGambarUrlAttribute($value)
    {
        return $value ? asset('storage/' . $value) : asset('images/default-food.jpg');
    }
}