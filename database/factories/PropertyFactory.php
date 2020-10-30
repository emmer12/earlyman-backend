<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Property;
use Faker\Generator as Faker;

$factory->define(Property::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'body' => $faker->sentences(3, true)
    ];
});
