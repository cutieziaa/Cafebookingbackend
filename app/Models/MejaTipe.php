<?php
// app/Models/MejaTipe.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MejaTipe extends Model
{
    use HasFactory;

    protected $table = 'meja_tipe';
    protected $fillable = ['nama_tipe', 'deskripsi'];

    public function meja()
    {
        return $this->hasMany(Meja::class, 'tipe_id');
    }
}