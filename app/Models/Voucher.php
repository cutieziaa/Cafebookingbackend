<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'voucher';

    protected $fillable = [
        'kode', 'diskon_persen', 'diskon_nominal',
        'minimum_order', 'limit_penggunaan', 'expired_at'
    ];
}
