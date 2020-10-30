<?php

namespace App\Jobs\Api\v1;

use App\User;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateComment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var int
     */
    protected $property_id;

    /**
     * @var \App\User
     */
    protected $user;

    public function __construct(string $body, int $property_id, User $user)
    {
        $this->body = $body;
        $this->property_id = $property_id;
        $this->user = $user;
    }

    public static function fromRequest($request, $user)
    {
        return new static(
            $request->body(),
            $request->property_id(),
            $user
        );
    }

    public function handle()
    {
        $comment = Comment::create([
            'body' => $this->body,
            'property_id' => $this->property_id,
            'user_id' => $this->user->id
        ]);

        return $comment;
    }
}
