<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CryptoAccount extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $attributes = [
        'btc' => 0,
        'eth' => 0,
        'ltc' => 0,
        'xrp' => 0,
        'link' => 0,
        'bat' => 0,
        'aave' => 0,
        'usdt' => 0,
        'xlm' => 0,
        'bch' => 0,
    ];
}
