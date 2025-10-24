<?php
// app/Models/Booking.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'booking';
    protected $fillable = [
        'pengguna_id', 'meja_id', 'tanggal_booking', 'waktu_mulai', 
        'durasi_minutes', 'jumlah_orang', 'total_amount', 'paid_amount',
        'payment_status', 'booking_status', 'assigned_by'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_booking' => 'date',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2'
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function meja()
    {
        return $this->belongsTo(Meja::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // Scope untuk status
    public function scopeMenunggu($query)
    {
        return $query->where('booking_status', 'menunggu');
    }

    public function scopeDikonfirmasi($query)
    {
        return $query->where('booking_status', 'dikonfirmasi');
    }

    public function getWaktuSelesaiAttribute()
    {
        return date('H:i', strtotime("$this->waktu_mulai + $this->durasi_minutes minutes"));
    }
}