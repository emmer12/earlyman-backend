<?php

namespace App\Jobs\Api\v1;

use App\Models\Blog;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessBlog implements ShouldQueue
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
     * @var \App\Models\Blog
     */
    protected $blog;

    public function __construct($title, $body, $user, $blog=null)
    {
        $this->title = $title;
        $this->body = $body;
        $this->user = $user;
        $this->blog = $blog;
    }

    public static function fromRequest($request, $user, $blog=null)
    {
        return new static(
            $request->title(),
            $request->body(),
            $user,
            $blog
        );
    }

    public function handle()
    {
        $blog = Blog::updateOrCreate(
            [
                'id' => (isset($this->blog->id)) ? $this->blog->id : null,
                'title' => (isset($this->blog->title))? $this->blog->title : null,
                'user_id' => (isset($this->blog->user_id)) ? $this->blog->user_id : null
            ],
            [
                'title' => $this->title,
                'body' => $this->body,
                'active' => Blog::ACTIVE,
                'user_id' => $this->user->id
            ]
        );

        return $blog;
    }
}
