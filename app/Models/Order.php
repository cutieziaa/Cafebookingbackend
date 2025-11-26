<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'booking_id',
        'pickup_id',
        'user_id',
        'total',
        'jenis_order',
        'status'
    ];

    // Relasi ke item
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Order untuk booking
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Order untuk pickup
    public function pickup()
    {
        return $this->hasOne(Pickup::class);
    }

    // Order milik user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
