<?php

namespace App\Jobs\Api\v1;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteProperty implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \App\Models\Property
     */
    protected $property;
    
    public function __construct(Property $property)
    {
        $this->property = $property;
    }

    public function handle()
    {
        $this->property->delete();
    }
}
