<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MejaTipe extends Model
{
    protected $table = 'meja_tipe';
    protected $fillable = ['nama'];

    public function meja()
    {
        return $this->hasMany(Meja::class);
    }
}
