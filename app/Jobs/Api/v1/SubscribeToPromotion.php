<?php

namespace App\Jobs\Api\v1;

use App\User;
use Carbon\Carbon;
use App\Models\Property;
use App\Models\PromotedPost;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SubscribeToPromotion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    protected $plan;

    /**
     * @var int
     */
    protected $duration;

    /**
     * @var \Carbon\Carbon
     */
    protected $start_date;

    /**
     * @var \Carbon\Carbon
     */
    protected $end_date;

    /**
     * @var \App\User
     */
    protected $user;

    /**
     * @var \App\Models\Property
     */
    protected $property;

    /**
     * @var int
     */
    protected $property_id;
    
    public function __construct($plan, $duration, $user, $property_id)
    {
        $this->plan = $plan;
        $this->user = $user;
        $this->property_id = $property_id;
        $this->duration = $duration;
        $this->start_date = date("Y-m-d H:i:s");
        $this->end_date = (is_numeric($duration)) ? Carbon::now()->addDays($this->duration) : $duration;
    }

    public function handle()
    {
        // If user is suibscrubed to a plan,
        // add a property to the promoted posts table.
        // if ($this->property != null) {
        //     PromotedPost::create([
        //         'property_id' => $this->property->id,
        //         'user_id' => $this->user->id,
        //         'plan' => $this->plan,
        //         'start_date' => $this->start_date,
        //         'end_date' => $this->end_date
        //     ]);

        //     return true;
        // }

        // $properties = $this->user->properties;

        // foreach ($properties as $property) {
        //     PromotedPost::create(
        //         [
        //             'property_id' => $property->id,
        //             'user_id' => $this->user->id,
        //             'plan' => $this->plan,
        //             'start_date' => $this->start_date,
        //             'end_date' => $this->end_date
        //         ]
        //     );
        // }

        PromotedPost::create([
            'property_id' => $this->property_id,
            'user_id' => $this->user->id,
            'plan' => $this->plan,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date
        ]);

        return true;
    }
}
