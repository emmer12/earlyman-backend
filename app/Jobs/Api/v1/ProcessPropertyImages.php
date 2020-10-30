<?php

namespace App\Jobs\Api\v1;

use Storage;
use App\User;
use App\Models\Image;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Image as ImageIntervention;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessPropertyImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var App\User
     */
    protected $user;

    /**
     * @var App\Models\Property
     */
    protected $property;

    protected $images;
    
    public function __construct($images, $user, $property)
    {
        $this->images = $images;
        $this->user = $user;
        $this->property = $property;
    }

    public static function fromRequest(PropertyRequest $request, User $user, Property $property)
    {
        return new static(
            $request->images(),
            $user,
            $property
        );
    }

    public function handle()
    {
        $image_ids = [];

        foreach($this->images as $image) {
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $location = 'public/propery_images/' . $this->property->id . '/' . $filename;

            $image = ImageIntervention::make($image);

            $save_to_storage = Storage::put($location, $image->encode());

            $saved_image = Image::create([
                'property_id' => $this->property->id,
                'image' => $filename
            ]);

            array_push($image_ids, $saved_image->id);
        }

        $property->images()->sync($image_ids);

        return $image_ids;
    }
}
