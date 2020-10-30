<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'bio', 
        'location', 
        'birthday',
        'phone',
        'address',
        'cover_image',
        'user_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'user_id',
        'id'
    ];

    protected $appends = ['cover_url'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getCoverUrlAttribute()
    {
        if ($this->cover_image != null) {
            return asset('storage/cover_images/' . $this->cover_image);
        } else {
            return null;
        }
    }
}
