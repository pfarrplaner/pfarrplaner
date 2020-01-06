<?php

use Faker\Generator as Faker;

$factory->define(App\Tag::class, function (Faker $faker) {
    $name = $faker->sentence();
    return [
        'code' => \Illuminate\Support\Str::slug($name),
        'name' => $name,
    ];
});
