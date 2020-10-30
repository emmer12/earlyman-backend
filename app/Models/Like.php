<?php

namespace App\Models;

use App\User;
use App\Models\Property;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $hidden = ['property_id', 'user_id', 'created_at', 'updated_at'];

    protected $fillable = [
        'property_id',
        'user_id'
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
