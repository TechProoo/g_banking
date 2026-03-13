<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'site_name', 'site_title', 'description', 'currency',
        'contact_email', 'location', 'captcha', 'enable_2fa', 'enable_kyc',
        'enable_verification', 'withdrawal_option', 'commission_type', 'commission_fee',
        'return_capital', 'should_cancel_plan', 'modules',
    ];

    protected $casts = [
        'return_capital' => 'boolean',
        'should_cancel_plan' => 'boolean',
        'modules' => 'array'
    ];

    // public function getModulesAttribute($value)
    // {
    //     return ucfirst($value);
    // }
}