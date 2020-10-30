<?php

namespace App\Jobs\Api\v1;

use Storage;
use App\User;
use App\Models\Blog;
use App\Models\Image;
use App\Models\Comment;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Image as ImageIntervention;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var App\User
     */
    protected $user;

    /**
     * @var \App\Models\Property
     */
    protected $property;

    /**
     * @var \App\Models\Comment
     */
    protected $comment;

    /**
     * @var \App\Models\Blog
     */
    protected $blog;

    protected $images;
    
    public function __construct($images, $user, $property=null, $comment=null, $blog=null)
    {
        $this->images = $images;
        $this->user = $user;
        $this->property = $property;
        $this->comment = $comment;
        $this->blog = $blog;
    }

    public static function fromRequest($request, User $user, Property $property=null, Comment $comment=null, Blog $blog=null)
    {
        return new static(
            $request->images(),
            $user,
            $property,
            $comment,
            $blog
        );
    }

    public function handle()
    {
        if ($this->property != null && $this->images != null) {
            $parent_directory = 'public/property_images/';
            $directory = 'public/property_images/' . $this->property->id;

            /**
             * To avoid duplication of images,
             * and property syncing with posts.
             * Delete every existing image in the db
             * and file system, then re-insert.
             */
            if (in_array($directory, Storage::directories($parent_directory))) {
                $this->property->images()->delete();
                Storage::deleteDirectory($directory);
            }

            // Add new images to db and save.
            foreach($this->images as $image) {
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $location = $directory . '/' . $filename;

                $image = ImageIntervention::make($image);

                $image->resize(500, null, function ($constraint) {
                    $constraint->aspectRatio();
                });

                $save_to_storage = Storage::put($location, $image->encode());

                $saved_image = Image::create([
                    'property_id' => $this->property->id,
                    'image' => $filename,
                    'type' => 'property'
                ]);
            }
        }

        if ($this->comment != null && $this->images != null) {
            // dd('Uploading...');
            $parent_directory = 'public/comment_images/';
            $directory = 'public/comment_images/' . $this->comment->id;

            /**
             * To avoid duplication of images,
             * and property syncing with posts.
             * Delete every existing image in the db,
             * and file system, then re-insert.
             */
            if (in_array($directory, Storage::directories($parent_directory))) {
                $this->comment->images()->delete();
                Storage::deleteDirectory($directory);
            }

            // Add new images to db and save.
            foreach($this->images as $image) {
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $location = $directory . '/' . $filename;

                $image = ImageIntervention::make($image);

                $save_to_storage = Storage::put($location, $image->encode());

                $saved_image = Image::create([
                    'comment_id' => $this->comment->id,
                    'image' => $filename,
                    'type' => 'comment'
                ]);
            }
        }

        if ($this->blog != null && $this->images != null) {
            // dd('Uploading...');
            $parent_directory = 'public/blog_images/';
            $directory = 'public/blog_images/' . $this->blog->id;

            /**
             * To avoid duplication of images,
             * and property syncing with posts.
             * Delete every existing image in the db,
             * and file system, then re-insert.
             */
            if (in_array($directory, Storage::directories($parent_directory))) {
                $this->blog->images()->delete();
                Storage::deleteDirectory($directory);
            }

            // Add new images to db and save.
            foreach($this->images as $image) {
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $location = $directory . '/' . $filename;

                $image = ImageIntervention::make($image);

                $save_to_storage = Storage::put($location, $image->encode());

                $saved_image = Image::create([
                    'blog_id' => $this->blog->id,
                    'image' => $filename,
                    'type' => 'blog'
                ]);
            }
        }

        return true;
    }
    
}
