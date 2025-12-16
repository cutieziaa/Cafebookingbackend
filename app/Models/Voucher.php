<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Voucher extends Model
{
    protected $table = 'voucher';

    protected $fillable = [
        'kode', 'nama', 'deskripsi',
        'tipe_diskon', 'diskon_persen', 'diskon_nominal',
        'maksimum_diskon', 'minimum_order', 'limit_penggunaan',
        'penggunaan_sekarang', 'status', 'tanggal_mulai',
        'expired_at', 'hanya_untuk_user_tertentu', 'user_ids'
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'tanggal_mulai' => 'date',
        'user_ids' => 'array',
        'minimum_order' => 'decimal:2',
        'diskon_nominal' => 'decimal:2',
        'maksimum_diskon' => 'decimal:2',
    ];

    // Scope untuk filter yang sering digunakan
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif')
                    ->where(function ($q) {
                        $q->whereNull('expired_at')
                          ->orWhere('expired_at', '>', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('tanggal_mulai')
                          ->orWhere('tanggal_mulai', '<=', now());
                    });
    }

    public function scopeBisaDigunakan($query, $userId = null)
    {
        return $query->aktif()
                    ->whereColumn('penggunaan_sekarang', '<', 'limit_penggunaan')
                    ->when($userId, function ($q) use ($userId) {
                        $q->where(function ($query) use ($userId) {
                            $query->where('hanya_untuk_user_tertentu', false)
                                  ->orWhereJsonContains('user_ids', (string) $userId);
                        });
                    });
    }

    // Accessor untuk status voucher
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
            'habis' => 'Habis'
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }

    // Method untuk cek apakah voucher masih berlaku
    public function isExpired()
    {
        return $this->expired_at && Carbon::now()->gt($this->expired_at);
    }

    public function isActive()
    {
        return $this->status === 'aktif' && !$this->isExpired();
    }

    // Method untuk hitung diskon
    public function hitungDiskon($total)
    {
        if (!$this->isActive()) {
            return 0;
        }

        if ($total < $this->minimum_order) {
            return 0;
        }

        if ($this->tipe_diskon === 'nominal') {
            return min($this->diskon_nominal, $total);
        } else {
            $diskon = $total * ($this->diskon_persen / 100);
            
            if ($this->maksimum_diskon && $diskon > $this->maksimum_diskon) {
                return $this->maksimum_diskon;
            }
            
            return $diskon;
        }
    }

    // Method untuk penggunaan voucher
    public function gunakan()
    {
        $this->increment('penggunaan_sekarang');
        
        if ($this->penggunaan_sekarang >= $this->limit_penggunaan) {
            $this->status = 'habis';
            $this->save();
        }
    }

    // Relasi ke Order
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}