<?php

use Faker\Generator as Faker;

$factory->define(\App\Location::class, function (Faker $faker) {
    $name = $faker->firstName('male');
    $name2 = $faker->firstName('male');
    return [
        'name' => $name.(substr($name, -1) == 's' ? '' : 's').'kirche',
        'city_id' => factory(\App\City::class),
        'default_time' => '',
        'cc_default_location' => $name2.(substr($name2, -1) == 's' ? '' : 's').'kirche',
    ];
});
