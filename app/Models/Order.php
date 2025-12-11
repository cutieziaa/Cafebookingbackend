<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Tambahkan 'voucher_id' dan 'discount_amount' ke fillable
    protected $fillable = [
        'booking_id',
        'user_id',
        'total',
        'jenis_order',
        'status',
        'voucher_id', // Tambahkan ini
        'discount_amount' // Tambahkan ini
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // pickup milik order
    public function pickup()
    {
        return $this->hasOne(Pickup::class);
    }

    // Tambahkan relasi ke Voucher
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}