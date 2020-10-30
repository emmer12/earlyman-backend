<?php

namespace App\Models;

use App\User;
use App\Models\Image;
use App\Models\Property;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'body',
        'property_id',
        'user_id'
    ];

    protected $hidden = ['updated_at'];

    public $with = ['user', 'images'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
