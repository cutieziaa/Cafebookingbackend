<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'booking';

    // 1. Tambahkan 'waktu_selesai' ke fillable
    protected $fillable = [
        'user_id', 
        'meja_id', 
        'tanggal',
        'waktu_selesai', // <-- TAMBAHKAN INI
        'jumlah_orang', 
        'status'
    ];

    // Opsional: Cast agar tanggal otomatis menjadi instance Carbon
    protected $casts = [
        'tanggal' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

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

    /**
     * 2. Accessor untuk menghitung durasi booking dalam jam.
     * Atribut virtual ini bisa diakses dengan $booking->durasi_jam
     *
     * @return float|null
     */
    public function getDurasiJamAttribute()
    {
        // Jika waktu_selesai belum diisi, return null
        if (is_null($this->waktu_selesai)) {
            return null;
        }

        // Hitung selisih dalam menit, lalu konversi ke jam (dalam bentuk desimal)
        return $this->waktu_selesai->diffInMinutes($this->tanggal) / 60.0;
    }
}