<?php

namespace App\Jobs\Api\v1;

use App\Models\Tag;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateProperty implements ShouldQueue
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
     * @var App\User
     */
    protected $user;

    /**
     * @var array
     */
    protected $tags;

    public function __construct($title, $body, $tags, $user)
    {
        $this->title = $title;
        $this->body = $body;
        $this->tags = $tags;
        $this->user = $user;
    }

    public static function fromRequest($request, $user)
    {
        return new static(
            $request->title(),
            $request->body(),
            $request->tags(),
            $user
        );
    }

    public function handle()
    {
        $property = Property::create([
            'title' => $this->title,
            'body' => $this->body,
            'active' => Property::ACTIVE,
            'user_id' => $this->user->id
        ]);

        $tag_ids = [];

        foreach ($this->tags as $tag) {
            $saved_tag = Tag::updateOrCreate(
                ['title' => $tag],
                ['title' => $tag]
            );
            array_push($tag_ids, $saved_tag->id);
        }
        
        $property->tags()->sync($tag_ids);

        return $property;
    }
}
