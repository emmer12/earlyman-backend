<?php

namespace App\Jobs\Api\User;

use Storage;
use App\User;
use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Image as ImageIntervention;
use App\Http\Requests\ProfileRequest;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateProfile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $firstname;

    /**
     * @var string
     */
    private $lastname;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $bio;

    /**
     * @var string
     */
    private $location;

    /**
     * @var string
     */
    private $birthday;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $username;

    /**
     * @var App\User
     */
    private $user;
    
    public function __construct($firstname, $lastname, $email, $username, $bio, $location, $birthday, $phone, $address, $user)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->username = $username;
        $this->bio = $bio;
        $this->location = $location;
        $this->birthday = $birthday;
        $this->phone = $phone;
        $this->address = $address;
        $this->user = $user;
    }

    public static function fromRequest(ProfileRequest $request, $user): self
    {
        return new static(
            $request->firstname(),
            $request->lastname(),
            $request->email(),
            $request->username(),
            $request->bio(),
            $request->location(),
            $request->birthday(),
            $request->phone(),
            $request->address(),
            $user
        );
    }
    
    public function handle()
    {
         $this->user->update([
            'firstname' => ($this->firstname == null) ? $this->user->firstname : $this->firstname,
            'lastname' => ($this->lastname == null) ? $this->user->lastname : $this->lastname,
            'email' => ($this->email == null) ? $this->user->email : $this->email,
            'username' => ($this->username == null) ? $this->user->username : $this->username
        ]);

        $profile = Profile::updateOrCreate(['user_id' => $this->user->id], [
                'bio' => $this->bio,
                'location' => $this->location,
                'birthday' => $this->birthday,
                'phone' => $this->phone,
                'address' => $this->address,
                'user_id' => $this->user->id
            ]);
        

        return $profile;
    }
}
