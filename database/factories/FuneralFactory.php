<?php

use Faker\Generator as Faker;

$factory->define(\App\Funeral::class, function (Faker $faker) {
    return [
        'service_id' => factory(\App\Service::class),
        'buried_name' => $faker->name,
        'buried_address' => $faker->streetAddress,
        'buried_zip' => $faker->postcode,
        'buried_city' => $faker->city,
        'text' => $faker->sentence(3),
        'announcement' => $faker->date('d.m.Y'),
        'type' => 'Erdbestattung',
        'wake' => null,
        'wake_location' => null,
        'relative_name' => $faker->name,
        'relative_address' => $faker->streetAddress,
        'relative_zip' => $faker->postcode,
        'relative_city' => $faker->city,
        'relative_contact_data' => $faker->sentence,
        'appointment' => $faker->date('d.m.Y H:i'),
        'dob' => $faker->date('d.m.Y'),
        'dod' => $faker->date('d.m.Y'),
        //
    ];
});
