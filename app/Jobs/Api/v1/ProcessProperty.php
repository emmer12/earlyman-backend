<?php

namespace App\Jobs\Api\v1;

use App\Models\Tag;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessProperty implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var \App\User
     */
    protected $user;

    /**
     * @var array
     */
    protected $tags;
    
    /**
     * @var \App\Models\Property
     */
    protected $property;

    public function __construct($title, $body, $tags, $user, $property=null)
    {
        $this->title = $title;
        $this->body = $body;
        $this->tags = $tags;
        $this->user = $user;
        $this->property = $property;
    }

    public static function fromRequest($request, $user, $property=null)
    {
        return new static(
            $request->title(),
            $request->body(),
            $request->tags(),
            $user,
            $property
        );
    }

    public function handle()
    {
        $property = Property::updateOrCreate(
            [
                'id' => (isset($this->property->id)) ? $this->property->id : null,
                'title' => (isset($this->property->title))? $this->property->title : null,
                'user_id' => (isset($this->property->user_id)) ? $this->property->user_id : null
            ],
            [
                'title' => $this->title,
                'body' => $this->body,
                'active' => Property::ACTIVE,
                'user_id' => $this->user->id
            ]
        );

        $tag_ids = [];

        foreach ($this->tags as $tag) {
            if ($tag !== null) {
                $saved_tag = Tag::updateOrCreate(
                    ['title' => $tag],
                    ['title' => $tag]
                );
                array_push($tag_ids, $saved_tag->id);
            }
        }
        
        $property->tags()->sync($tag_ids);

        // $isPromoted = $this->user->hasSubscribedForPromotion($property->id);

        // if ($isPromoted) {
        //     SubscribeToPromotion::dispatchNow($isPromoted->plan, $isPromoted->end_date, auth()->user(), $property);
        // }

        return $property;
    }
}
