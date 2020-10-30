<?php

namespace App\Models;

use App\Models\Blog;
use App\Models\Comment;
use App\Models\Property;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'property_id',
        'comment_id',
        'blog_id',
        'image',
        'type'
    ];

    protected $hidden = ['type', 'created_at', 'updated_at'];

    protected $appends = ['image_url'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function comment()
    {
        return $this->belongsTo(Image::class, 'comment_id');
    }

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    public function getImageUrlAttribute()
    {
        if ($this->property_id != null) {
            return asset('storage/property_images/' . $this->property_id . '/' . $this->image);
        }

        if ($this->comment_id != null) {
            return asset('storage/comment_images/' . $this->comment_id . '/' . $this->image);
        }

        if ($this->blog_id != null) {
            return asset('storage/blog_images/' . $this->blog_id . '/' . $this->image);
        }
    }
}
