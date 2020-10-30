<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use SoftDeletes;

    const ACTIVE = true;
    const SUSPENDED = false;

    protected $fillable = [
        'title',
        'body',
        'active',
        'user_id'
    ];

    protected $appends = ['date_posted'];

    public $with = ['user', 'images'];

    protected $hidden = [
        'user_id',
        'active',
        'deleted_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

     public function getDatePostedAttribute()
    {
        return $this->created_at->diffForHumans(null, true, true);
    }
}
