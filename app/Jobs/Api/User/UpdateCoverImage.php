<?php

namespace App\Jobs\Api\User;

use Storage;
use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Image as ImageIntervention;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Requests\UpdateImageRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateCoverImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cover_image;

    /**
     * @var App\User
     */
    protected $user;

    public function __construct($cover_image, $user)
    {
        $this->cover_image = $cover_image;
        $this->user = $user;
    }

    public static function fromRequest(UpdateImageRequest $request, $user)
    {
        return new static(
            $request->cover_image(),
            $user
        );
    }

    public function handle()
    {
        $image = $this->cover_image;
        $filename = time() . '.' . $image->getClientOriginalExtension();
        $location = 'public/cover_images/' . $filename;

        $image = ImageIntervention::make($image);

        $save_to_storage = Storage::put($location, $image->encode());

        $profile = Profile::updateOrCreate(
                        ['user_id' => $this->user->id],
                        ['cover_image' => $filename, 'user_id' => $this->user->id]
                    );

        return $profile;
    }
}
