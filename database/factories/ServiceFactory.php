<?php

use Faker\Generator as Faker;

$factory->define(
    App\Service::class,
    function (Faker $faker) {
        $church = $faker->name('male');
        $church .= (substr($church, -1) == 's' ? '' : 's') . 'kirche';
        return [
            'day_id' => factory(\App\Day::class),
            'location_id' => factory(\App\Location::class),
            'city_id' => factory(\App\City::class),
            'time' => $faker->time('H:i'),
            'description' => $faker->sentence(),
            'need_predicant' => $faker->numberBetween(0, 1),
            'baptism' => $faker->numberBetween(0, 1),
            'eucharist' => $faker->numberBetween(0, 1),
            'offerings_counter1' => $faker->name(),
            'offerings_counter2' => $faker->name(),
            'offering_goal' => $faker->sentence(),
            'offering_description' => $faker->sentence(),
            'offering_type' => $faker->randomElement(['PO', 'eO', '']),
            'cc' => $faker->numberBetween(0, 1),
            'cc_alt_time' => $faker->time('H:i'),
            'cc_location' => $church,
            'cc_lesson' => $faker->sentence(),
            'cc_staff' => $faker->firstName() . ', ' . $faker->firstName(),
            'internal_remarks' => $faker->sentence(),
            'offering_amount' => $faker->randomFloat(2),
        ];
    }
);
