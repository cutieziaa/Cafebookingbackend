<?php
// app/Models/OrderItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_item';
    protected $fillable = ['order_id', 'menu_id', 'jumlah', 'subtotal'];

    protected $casts = [
        'subtotal' => 'decimal:2'
    ];

    public function order()
    {
        return $this->belongsTo(OrderPickup::class, 'order_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}