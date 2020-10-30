<?php

namespace App\Jobs\Api\v1;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteComment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \App\Models\Comment
     */
    protected $comment;
    
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function handle()
    {
        $this->comment->images()->delete();
        $this->comment->delete();
    }
}
