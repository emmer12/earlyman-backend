<?php

namespace App\Jobs\Api\User;

use App\User;
use Storage;
use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Image as ImageIntervention;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Requests\UpdateImageRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateProfileImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $profile_image;

    /**
     * @var App\User
     */
    protected $user;

    public function __construct($profile_image, $user)
    {
        $this->profile_image = $profile_image;
        $this->user = $user;
    }

    public static function fromRequest(UpdateImageRequest $request, $user)
    {
        return new static(
            $request->profile_image(),
            $user
        );
    }

    public function handle()
    {
        $image = $this->profile_image;
        $filename = time() . '.' . $image->getClientOriginalExtension();
        $location = 'public/avatars/' . $this->user->id . '/' . $filename;

        $image = ImageIntervention::make($image);

        $save_to_storage = Storage::put($location, $image->encode());

        $user = User::updateOrCreate(
                        ['id' => $this->user->id],
                        ['avatar' => $filename]
                    );

        return $user;
    }
}
