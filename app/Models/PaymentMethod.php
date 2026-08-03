<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'payment_settings';

    protected $fillable = [

        'name',

        'type',

        'account_number',

        'account_name',

        'image',

        'notes',

        'is_active',

        'sort_order',

    ];

    protected $casts = [

        'is_active' => 'boolean',

    ];
}
