<?php

namespace App\Jobs\Api\User;

use Avatar;
use Storage;
use App\User;
use Illuminate\Bus\Queueable;
use App\Exceptions\CannotCreateUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Queue\SerializesModels;
use App\Http\Requests\RegisterRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RegisterUser implements ShouldQueue
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
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var boolean
     */
    private $is_admin;

    public function __construct(string $firstname, string $lastname, string $email, string $username, string $password, $is_admin=false)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->is_admin = ($is_admin == null) ? false : true;
    }

    public static function fromRequest(RegisterRequest $request): self
    {
        return new static(
            $request->firstname(),
            $request->lastname(),
            $request->email(),
            $request->username(),
            $request->password(),
            $request->is_admin()
        );
    }

    public function handle()
    {
        $this->assertEmailAddressIsUnique($this->email);
        $this->assertUsernameIsUnique($this->username);

        $user = new User([
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'username' => strtolower($this->username),
            'password' => Hash::make($this->password),
            'status' => User::ACTIVE,
            'active' => false,
            'is_admin' => $this->is_admin,
            'activation_token' => str_random(60)
        ]);

        $user->save();

        $avatar = Avatar::create($user->name())->getImageObject()->encode('png');
        Storage::put('public/avatars/'.$user->id.'/avatar.png', (string) $avatar);

        return $user;
    }

    private function assertEmailAddressIsUnique(string $email)
    {
        try {
            User::findByEmail($email);
        } catch (ModelNotFoundException $exception) {
            return true;
        }

        throw CannotCreateUser::duplicateEmail($email);
    }

    public function assertUsernameIsUnique(string $username)
    {
        try {
            User::findByUsername($username);
        } catch (ModelNotFoundException $exception) {
            return true;
        }

        throw CannotCreateUser::duplicateUsername($username);
    }
}
