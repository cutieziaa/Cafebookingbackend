<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meja extends Model
{
    protected $table = 'meja';
    protected $fillable = ['meja_tipe_id', 'nomor', 'tersedia'];

    public function tipe()
    {
        return $this->belongsTo(MejaTipe::class, 'meja_tipe_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
