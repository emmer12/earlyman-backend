<?php

namespace Tests\Unit\Jobs;

use App\User;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use App\Jobs\Api\User\UpdateProfile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function users_can_update_profile()
    {
        $this->artisan('passport:install');

        Storage::fake('local');

        $file = UploadedFile::fake()->image('cover.jpg');

        $user = factory(User::class)->create();

        $profile = UpdateProfile::dispatchNow('I am human', 'Lagos, NIgeria', 'April, 29', $file, true, $user);

        $this->assertEquals('I am human', $profile->bio);

        Storage::disk('local')->assertExists('public/cover_images/' . $profile->cover_image);
    }
}
