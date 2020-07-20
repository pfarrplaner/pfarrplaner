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

namespace App\Console\Commands;

use App\Baptism;
use App\City;
use App\Comment;
use App\Funeral;
use App\Location;
use App\Parish;
use App\User;
use App\Wedding;
use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

/**
 * Class DemoBuilder
 * @package App\Console\Commands
 */
class DemoBuilder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build a new demo site';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $universalPassword = Hash::make('test');

        $dbName = Config::get('database.connections.' . Config::get('database.default') . '.database');
        $this->line('Demo builder, using database "' . $dbName . '"');
        if (!env('DEMO_MODE')) {
            $this->error('Demo builder requires DEMO_MODE=1 to be set in .env, aborting.');
            return;
            die();
        }
        if (false === strpos($dbName, 'demo')) {
            $this->error('Demo builder requires database name to contain "demo", aborting.');
            return;
            die();
        }
        $this->info('Passed all demo checks.');
        $faker = Factory::create('de_DE');


        $this->line('Creating cities...');
        $cities = City::all();
        foreach ($cities as $city) {
            $oldName = $city->name;
            $city->name = $faker->city;
            $city->public_events_calendar_url = $city->op_domain = $city->op_customer_key = $city->op_customer_token = '';
            $city->konfiapp_apikey = '';
            $city->save();
            $locations = Location::where('city_id', $city->id)->get();
            /** @var Location $location */
            foreach ($locations as $location) {
                $location->name = str_replace($oldName, $city->name, $location->name);
                $location->save();
            }
        }
        $this->info(count($cities) . ' cities created.');

        $this->line('Creating users...');
        $users = User::all();
        foreach ($users as $user) {
            if ($user->name != 'Admin') {
                $user->first_name = $faker->firstName;
                $user->last_name = $faker->lastName;
                $user->name = $user->first_name . ' ' . $user->last_name;
                $user->address = $faker->address;
                $user->phone = $faker->phoneNumber;
                if ($user->password != '') {
                    $user->password = $universalPassword;
                }
                $user->email = strtolower($user->first_name . '.' . $user->last_name) . '@demo.pfarrplaner.de';
            } else {
                $user->password = Hash::make('admin');
            }
            $user->save();
        }
        $this->info(count($users) . ' users created.');

        $this->line('Creating baptisms...');
        $baptisms = Baptism::all();
        /** @var Baptism $baptism */
        foreach ($baptisms as $baptism) {
            $baptism->candidate_name = $faker->name;
            $baptism->candidate_address = $faker->streetAddress;
            $baptism->candidate_zip = $faker->postcode;
            $baptism->candidate_city = $faker->city;
            $baptism->candidate_phone = $faker->phoneNumber;
            $baptism->candidate_email = $faker->email;
            $baptism->first_contact_with = $faker->name;
            $baptism->save();
        }
        $this->info(count($baptisms) . ' baptisms created.');

        $this->line('Creating weddings...');
        $weddings = Wedding::all();
        /** @var Wedding $wedding */
        foreach ($weddings as $wedding) {
            $wedding->spouse1_name = $faker->name('male');
            $wedding->spouse1_phone = $faker->phoneNumber;
            $wedding->spouse1_email = $faker->email;
            if ($wedding->spouse1_birth_name != '') {
                $wedding->spouse1_birth_name = $faker->lastName;
            }
            $wedding->spouse2_name = $faker->name('female');
            $wedding->spouse2_phone = $faker->phoneNumber;
            $wedding->spouse2_email = $faker->email;
            if ($wedding->spouse2_birth_name != '') {
                $wedding->spouse2_birth_name = $faker->lastName;
            }
            $wedding->save();
        }
        $this->info(count($weddings) . ' weddings created.');

        $this->line('Creating funerals...');
        $funerals = Funeral::all();
        /** @var Funeral $funeral */
        foreach ($funerals as $funeral) {
            $funeral->buried_name = $faker->name;
            $funeral->buried_address = $faker->streetAddress;
            $funeral->buried_zip = $faker->postcode;
            $funeral->buried_city = $faker->city;
            $funeral->relative_name = $faker->name;
            $funeral->relative_address = $faker->streetAddress;
            $funeral->relative_zip = $faker->postcode;
            $funeral->relative_city = $faker->city;
            $funeral->save();
        }
        $this->info(count($funerals) . ' funerals created.');

        $this->line('Creating parishes...');
        $parishes = Parish::with('owningCity')->get();
        /** @var Parish $parish */
        foreach ($parishes as $parish) {
            $name = str_replace('Pfarramt ', '', $parish->name);
            $parish->code = $parish->owningCity->name . ' ' . $name;
            $parish->address = $faker->streetAddress;
            $parish->zip = $faker->postcode;
            $parish->city = $parish->owningCity->name;
            $parish->phone = $faker->phoneNumber;
            $parish->email = $faker->email;
            $parish->save();
        }
        $this->info(count($parishes) . ' parishes created.');

        Comment::query()->delete();
        $this->info('All comments deleted');
    }
}
