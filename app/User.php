<?php

namespace App;

use Carbon\Carbon;
use App\Models\Like;
use App\Models\Comment;
use App\Models\Payment;
use App\Models\Property;
use App\Models\PromotedPost;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    const ACTIVE = 1;
    const BLOCKED = 0;
    const VERIFIED = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 
        'lastname',
        'email',
        'username',
        'password',
        'avatar',
        'active',
        'is_admin',
        'activation_token',
        'status',
        'verified'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'activation_token',
        'deleted_at',
        'email_verified_at',
        'created_at',
        'updated_at',

    ];

    protected $appends = ['avatar_url', 'is_subscribed_to_promo'];

    protected $with = ['profile'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->hasOne('App\Models\Profile');
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function promoted_posts()
    {
        return $this->hasMany(PromotedPost::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public static function findByEmail(string $email): self
    {
        return static::where('email', $email)->firstOrFail();
    }

    public static function hasSubscribedForPromotion($property_id)
    {
        $promoted_post = PromotedPost::where('user_id', auth()->user()->id)
                                           ->whereDate('end_date', '<=', Carbon::now()->toDateString())
                                           ->first();

        if ($promoted_post) {
            return $promoted_post;
        } else {
            return false;
        }
    }
 
    public static function findByUsername(string $username): self
    {
        return static::where('username', $username)->firstOrFail();
    }

    public function name(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getAvatarUrlAttribute()
    {
        return asset('storage/avatars/' . $this->id . '/' . $this->avatar);
    }

    public function getIsSubscribedToPromoAttribute()
    {
        try {
            $subscription_details = Payment::latest()->where('user_id', $this->id)->firstOrFail();
            if ($subscription_details->isCurrent) {
                return true;
            }
            return false;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }
}
