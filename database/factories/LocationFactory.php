<?php

use Faker\Generator as Faker;

$factory->define(\App\Location::class, function (Faker $faker) {
    $name = $faker->firstName('male');
    return [
        'name' => $name.(substr($name, -1) == 's' ? '' : 's').'kirche',
        'city_id' => factory(\App\City::class),
        'default_time' => '',
    ];
});
