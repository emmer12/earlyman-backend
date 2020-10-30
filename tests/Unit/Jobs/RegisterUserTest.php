<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Jobs\Api\User\RegisterUser;
use App\Exceptions\CannotCreateUser;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_create_a_user()
    {
        $this->artisan('passport:install');

        $user = RegisterUser::dispatchNow('John', 'Doe', 'johndoe@example.com', 'johndoe', 'password');

        $this->assertEquals('John Doe', $user->name());
    }

    /** @test */
    public function we_cannot_create_a_user_with_same_email_address()
    {
        $this->artisan('passport:install');

        $this->expectException(CannotCreateUser::class);

        RegisterUser::dispatchNow('John', 'Doe', 'johndoe@example.com', 'johndoe', 'password');
        RegisterUser::dispatchNow('John', 'Doe', 'johndoe@example.com', 'john', 'password');
    }

    /** @test */
    public function we_cannot_create_a_user_with_same_username()
    {
        $this->artisan('passport:install');
        
        $this->expectException(CannotCreateUser::class);

        RegisterUser::dispatchNow('John', 'Doe', 'johndoe@example.com', 'johndoe', 'password');
        RegisterUser::dispatchNow('John', 'Doe', 'doe@example.com', 'johndoe', 'password');
    }
}
