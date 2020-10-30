<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'payment_ref',
        'transaction_number',
        'user_id',
        'property_id',
        'amount',
        'currency',
        'plan',
        'duration',
        'isCurrent',
        'payment_status',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
