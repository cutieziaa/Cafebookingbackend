<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'booking_id', 'pickup_id', 'user_id',
        'total', 'jenis_order', 'status'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function pickup()
    {
        return $this->belongsTo(Pickup::class);
    }
}
