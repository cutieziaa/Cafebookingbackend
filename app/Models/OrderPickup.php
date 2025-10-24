<?php
// app/Models/OrderPickup.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPickup extends Model
{
    use HasFactory;

    protected $table = 'order_pickup';
    protected $fillable = [
        'pengguna_id', 'order_code', 'total_amount', 'paid_amount',
        'payment_status', 'pickup_type', 'order_status'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function generateOrderCode()
    {
        return 'ORD' . date('Ymd') . str_pad(OrderPickup::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
    }
}