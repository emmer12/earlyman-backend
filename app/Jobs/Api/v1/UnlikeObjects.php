<?php

namespace App\Jobs\Api\v1;

use App\User;
use App\Models\Like;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UnlikeObjects implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * \App\Models\Property
     */
    protected $property;

    /**
     * \App\User
     */
    protected $user;

    public function __construct(Property $property, User $user)
    {
        $this->property = $property;
        $this->user = $user;
    }

    public function handle()
    {
        $likes = Like::where('property_id', $this->property->id)
                        ->where('user_id', $this->user->id)
                        ->first()
                        ->delete();

        return true;
    }
}
