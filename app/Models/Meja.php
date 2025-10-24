<?php
// app/Models/Meja.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meja extends Model
{
    use HasFactory;

    protected $table = 'meja';
    protected $fillable = ['tipe_id', 'kode_meja', 'kapasitas', 'status'];

    public function tipe()
    {
        return $this->belongsTo(MejaTipe::class, 'tipe_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}