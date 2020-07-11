<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
 * @version git: $Id$
 *
 * Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
 *
 * Pfarrplaner is based on the Laravel framework (https://laravel.com).
 * This file may contain code created by Laravel's scaffolding functions.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
