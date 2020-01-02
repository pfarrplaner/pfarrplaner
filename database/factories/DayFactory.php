<?php

use Faker\Generator as Faker;

$factory->define(\App\Day::class, function (Faker $faker) {
    return [
        'date' => (new Carbon\Carbon($faker->date()))->format('d.m.Y'),
        'day_type' => $faker->randomElement([\App\Day::DAY_TYPE_DEFAULT, \App\Day::DAY_TYPE_LIMITED]),
        'name' => $faker->randomAscii,
        'description' => $faker->randomAscii,
    ];
});
