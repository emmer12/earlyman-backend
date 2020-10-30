<?php

namespace App\Http\Controllers\Api\v1;

use Auth;
use App\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Jobs\Api\User\UpdateProfile;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Jobs\Api\User\UpdateCoverImage;
use App\Http\Requests\UpdateImageRequest;
use App\Jobs\Api\User\UpdateProfileImage;
use App\Http\Resources\Api\v1\User as UserResource;

class ProfileController extends Controller
{
    public function updateProfile(ProfileRequest $request)
    {
        if ($request->validator->fails()) {
            $errors = $request->validator->messages();
            return response()->json([
                'success' => false,
                'code' => 'INVALID_FIELD',
                'message' => 'Invalid fields. Check form again.',
                'errors' => $errors
            ], 400);
        }

        $profile = $this->dispatchNow(UpdateProfile::fromRequest($request, auth()->user()));

        $user = User::find(auth()->id());

        return (new UserResource($user))
                        ->success(true)
                        ->code('PROFILE_UPDATED')
                        ->message('Your profile has been updated successfully.')
                        ->response()
                        ->setStatusCode(200);
    }

    public function showProfile(Request $request)
    {
        if ($request->exists('username')) {
            $user = User::findByUsername($request->get('username'));
        } else if ($request->exists('email')) {
            $user = User::findByEmail($request->get('email'));
        } else {
            $user = auth()->guard('api')->user();
        }

        return (new UserResource($user))
                        ->success(true)
                        ->code('USER_PROFILE')
                        ->message('User profile.')
                        ->response()
                        ->setStatusCode(200);
    }

    public function updateImage(UpdateImageRequest $request)
    {
        if ($request->validator->fails()) {
            $errors = $request->validator->messages();
            return (new ErrorResource(Error::errors($errors)))
                        ->success(false)
                        ->code('INVALID_FIELD')
                        ->message('Invalid fields. Check form and try again.')
                        ->response()
                        ->setStatusCode(400);
        }

        $username = $request->username();

        $user = User::findByUsername($username);

        $hasCoverImage = $request->hasFile('cover_image');
        $hasProfileImage = $request->hasFile('profile_image');

        if ($hasCoverImage && $hasProfileImage) {
            $coverimage = $this->dispatchNow(UpdateCoverImage::fromRequest($request, $user));
            $profileimage = $this->dispatchNow(UpdateProfileImage::fromRequest($request, $user));
        } elseif ($hasCoverImage) {
            $coverimage = $this->dispatchNow(UpdateCoverImage::fromRequest($request, $user));
        } elseif ($hasProfileImage) {
            $profileimage = $this->dispatchNow(UpdateProfileImage::fromRequest($request, $user));
        }

        return (new UserResource($user))
                        ->success(true)
                        ->code('IMAGE_UPDATED')
                        ->message('User image has been updated.')
                        ->response()
                        ->setStatusCode(200);
    }
}
