<?php

namespace Tests\Unit\Models;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_find_by_username()
    {
        $user = factory(User::class)->create(['username' => 'johndoe']);

        $this->assertInstanceOf(User::class, User::findByUsername('johndoe'));
    }

    /** @test */
    public function it_can_find_by_email()
    {
        $user = factory(User::class)->create(['email' => 'johndoe@gmail.com']);

        $this->assertInstanceOf(User::class, User::findByEmail('johndoe@gmail.com'));
    }
}
