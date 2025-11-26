<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pickup extends Model
{
    protected $table = 'pickup';

    protected $fillable = [
        'user_id',
        'order_id',
        'nama_penerima',
        'catatan',
        'status'
    ];

    // relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // relasi ke order (pickup belongsTo order)
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
