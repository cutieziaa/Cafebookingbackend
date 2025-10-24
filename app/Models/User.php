<?php
// app/Models/User.php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'email', 
        'password',
        'peran',
        'nomor_wa'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Scope untuk peran
    public function scopeCustomer($query)
    {
        return $query->where('peran', 'customer');
    }

    public function scopeAdmin($query)
    {
        return $query->where('peran', 'admin');
    }

    public function scopeCs($query)
    {
        return $query->where('peran', 'cs');
    }

    // Relasi
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'pengguna_id');
    }

    // Check peran
    public function isAdmin()
    {
        return $this->peran === 'admin';
    }

    public function isCs()
    {
        return $this->peran === 'cs';
    }

    public function isCustomer()
    {
        return $this->peran === 'customer';
    }
}