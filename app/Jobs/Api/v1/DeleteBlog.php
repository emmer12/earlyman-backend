<?php

namespace App\Jobs\Api\v1;

use App\Models\Blog;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteBlog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \App\Models\Blog
     */
    protected $blog;
    
    public function __construct(Blog $blog)
    {
        $this->blog = $blog;
    }

    public function handle()
    {
        $this->blog->delete();
    }
}
