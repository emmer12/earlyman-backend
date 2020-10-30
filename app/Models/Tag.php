<?php

namespace App\Models;

use App\Models\Property;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['title'];

    protected $hidden = [
        'updated_at',
        'pivot'
    ];

    public function properties()
    {
        return $this->belongsToMany(Property::class);
    }
}
