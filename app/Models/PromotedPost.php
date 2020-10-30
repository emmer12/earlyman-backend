<?php

namespace App\Models;

use App\User;
use App\Models\Property;
use Illuminate\Database\Eloquent\Model;

class PromotedPost extends Model
{
    protected $fillable = [
        'property_id',
        'user_id',
        'plan',
        'start_date',
        'end_date',
        'views',
    ];


    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
