<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    protected $table = 'booking';

    protected $fillable = [
        'user_id', 
        'meja_id', 
        'kode_booking', // Tambahkan ini
        'tanggal',
        'waktu_selesai',
        'jumlah_orang', 
        'status',
        'catatan' // Tambahkan ini
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    // Boot method untuk membuat kode booking otomatis
    protected static function boot()
    {
        parent::boot();

        // Buat kode booking saat membuat booking baru
        static::creating(function ($booking) {
            if (empty($booking->kode_booking)) {
                $booking->kode_booking = 'BKJ-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meja()
    {
        return $this->belongsTo(Meja::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function getDurasiJamAttribute()
    {
        if (is_null($this->waktu_selesai)) {
            return null;
        }

        return $this->waktu_selesai->diffInMinutes($this->tanggal) / 60.0;
    }
}