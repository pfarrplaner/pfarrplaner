<?php

use Faker\Generator as Faker;

$factory->define(\App\Location::class, function (Faker $faker) {
    $name = $faker->firstName('male');
    return [
        'name' => $name.(substr($name, -1) == 's' ? '' : 's').'kirche',
        'city_id' => function() {
            return factory(\App\City::class)->create()->id;
        },
        'default_time' => '',
    ];
});
