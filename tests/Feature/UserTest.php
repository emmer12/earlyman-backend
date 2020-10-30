<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function we_can_login_a_user()
    {  
        $this->artisan('passport:install');

        $user = factory(User::class)->create([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john@doe.com',
            'username' => 'johndoe'
        ]);

        $payload = [
            'email' => $user->email,
            'password' => 'secret'
        ];

        $response = $this->json('POST', 'http://127.0.0.1:8000/api/v1/auth/login', $payload);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function we_can_show_user_profile()
    {
        $this->artisan('passport:install');

        $user = factory(User::class)->create([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john@doe.com',
            'username' => 'johndoe'
        ]);

        $this->actingAs($user);
        
        $response = $this->json('GET', 'http://127.0.0.1:8000/api/v1/profile?username=johndoe');

        $response->assertStatus(200);
    }
}
