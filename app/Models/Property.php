<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Like;
use App\Models\Image;
use App\Models\Comment;
use App\Models\PromotedPost;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
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

    protected $appends = ['date_posted', 'likes_count', 'is_liked', 'is_commented'];

    public $with = ['user', 'tags', 'images', 'comments'];

    protected $hidden = [
        'user_id',
        'active',
        'deleted_at',
        'updated_at'
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function promoted_posts()
    {
        return $this->hasMany(PromotedPost::class);
    }

    public function getDatePostedAttribute()
    {
        return $this->created_at->diffForHumans(null, true, true);
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getIsLikedAttribute()
    {
        $user_id = (auth()->guard('api')->user()) ? auth()->guard('api')->user()->id : null;

        $likes_count = Like::where('property_id', $this->id)->where('user_id', $user_id)->get()->count();
        
        if ($likes_count == 1) {
            return true;
        }

        return false;
    }

    public function getIsCommentedAttribute()
    {
        $user_id = (auth()->guard('api')->user()) ? auth()->guard('api')->user()->id : null;

        $comment_count = Comment::where('property_id', $this->id)->where('user_id', $user_id)->get()->count();
        
        if ($comment_count == 1) {
            return true;
        }

        return false;
    }
}
