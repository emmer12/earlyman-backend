<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\User;
use App\Models\Profile;
use Faker\Generator as Faker;

$factory->define(Profile::class, function (Faker $faker) {
    return [
        'bio' => $faker->sentences(3, true),
        'location' => $faker->address,
        'birthday' => $faker->date('Y-m-d', 'now'),
        'cover_image' => $faker->image('/tmp', 640, 480),
        'user_id' => $faker->unique()->numberBetween(1, User::count())
    ];
});
