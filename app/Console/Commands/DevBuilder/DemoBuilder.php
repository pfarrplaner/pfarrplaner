<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/pfarrplaner/pfarrplaner
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

namespace App\Console\Commands\DevBuilder;

use App\Absence;
use App\Baptism;
use App\City;
use App\Comment;
use App\Funeral;
use App\Location;
use App\Parish;
use App\StreetRange;
use App\User;
use App\Wedding;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

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
    protected string $signature = 'demo:build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build a new demo site';

    /** @var Generator */
    protected Generator $faker;

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $dbName = Config::get('database.connections.' . Config::get('database.default') . '.database');
        $this->line('Demo builder, using database "' . $dbName . '"');
        if (!env('DEMO_MODE')) {
            $this->error('Demo builder requires DEMO_MODE=1 to be set in .env, aborting.');
            return;
        }
        if (!str_contains($dbName, 'demo')) {
            $this->error('Demo builder requires database name to contain "demo", aborting.');
            return;
        }
        $this->info('Passed all demo checks.');
        $this->faker = Factory::create('de_DE');

        foreach (
            [
                'users',
                'cities',
                'absences',
                'baptisms',
                'comments',
                'funerals',
                'parishes',
                'streetRanges',
                'weddings'
            ] as $unit
        ) {
            $methodName = 'handle' . ucfirst($unit);
            if (method_exists($this, $methodName)) {
                $this->$methodName();
            }
        }
    }

    protected function handleAbsences()
    {
        $this->line('Creating absences...');
        $absences = Absence::all();
        foreach ($absences as $absence) {
            $absence->update([
                                 'admin_notes' => $this->faker->text(),
                                 'approver_notes' => $this->faker->text(),
                                 'replacement_notes' => $this->faker->text(),
                             ]);
        }
        $this->info(count($absences) . ' absences created.');
    }

    protected function handleBaptisms()
    {
        $this->line('Creating baptisms...');
        $baptisms = Baptism::all();
        /** @var Baptism $baptism */
        foreach ($baptisms as $baptism) {
            $baptism->update([
                                 'candidate_name' => $this->faker->name,
                                 'candidate_address' => $this->faker->streetAddress,
                                 'candidate_zip' => $this->faker->postcode,
                                 'candidate_city' => $this->faker->city,
                                 'candidate_phone' => $this->faker->phoneNumber,
                                 'candidate_email' => $this->faker->email,
                                 'first_contact_with' => $this->faker->name,
                                 'notes' => $this->faker->text(),
                                 'dimissorial_issuer' => 'Pfarramt ' . $this->faker->city,
                                 'dimissorial_requested',
                                 'dimissorial_received',
                             ]);
        }
        $this->info(count($baptisms) . ' baptisms created.');
    }

    protected function handleCities()
    {
        $this->line('Creating cities...');
        $cities = City::all();
        foreach ($cities as $city) {
            $oldName = $city->name;
            $newName = $this->faker->city;

            $city->update([
                              'name' => $newName,
                              'public_events_calendar_url' => '',
                              'op_domain' => '',
                              'op_customer_key' => '',
                              'op_customer_token' => '',
                              'podcast_title' => '',
                              'podcast_logo' => '',
                              'sermon_default_image' => '',
                              'homepage' => '',
                              'podcast_owner_name' => '',
                              'podcast_owner_email' => '',
                              'google_auth_code' => '',
                              'google_access_token' => '',
                              'google_refresh_token' => '',
                              'youtube_channel_url' => '',
                              'konfiapp_apikey' => '',
                              'youtube_active_stream_id' => '',
                              'youtube_passive_stream_id' => '',
                              'youtube_auto_startstop' => '',
                              'youtube_cutoff_days' => '',
                              'default_offering_url' => '',
                              'communiapp_url' => '',
                              'communiapp_token' => '',
                              'communiapp_default_group_id' => '',
                              'konfiapp_default_type' => '',
                              'official_name' => 'Evangelische Kirchengemeinde ' . $newName,
                              'logo' => '',
                          ]);

            $locations = Location::where('city_id', $city->id)->get();
            /** @var Location $location */
            foreach ($locations as $location) {
                $location->update(['name' => str_replace($oldName, $newName, $location->name)]);
            }
        }
        $this->info(count($cities) . ' cities created.');
    }

    protected function handleComments()
    {
        Comment::query()->delete();
        $this->info('All comments deleted');
    }

    protected function handleFunerals()
    {
        $this->line('Creating funerals...');
        $funerals = Funeral::all();
        /** @var Funeral $funeral */
        foreach ($funerals as $funeral) {
            $funeral->update([
                                 'buried_name' => $this->faker->name,
                                 'buried_address' => $this->faker->streetAddress,
                                 'buried_zip' => $this->faker->postcode,
                                 'buried_city' => $this->faker->city,
                                 'relative_name' => $this->faker->name,
                                 'relative_address' => $this->faker->streetAddress,
                                 'relative_zip' => $this->faker->zip,
                                 'relative_city' => $this->faker->city,
                                 'relative_contact_data' => $this->faker->phoneNumber,
                                 'appointment',
                                 'dob',
                                 'dod',
                                 'spouse' => $this->faker->name,
                                 'parents' => $this->faker->name('male') . ' / ' . $this->faker->name('female'),
                                 'children' => join(', ', [$this->faker->name, $this->faker->name, $this->faker->name]),
                                 'further_family' => join(', ', [$this->faker->name, $this->faker->name]),
                                 'baptism' => '',
                                 'confirmation' => '',
                                 'undertaker' => $this->faker->name . ' (' . $this->faker->phoneNumber . ')',
                                 'eulogies' => '',
                                 'notes' => $this->faker->text(),
                                 'announcements' => $this->faker->text(),
                                 'childhood' => $this->faker->text(),
                                 'profession' => '',
                                 'family' => $this->faker->text(),
                                 'further_life' => $this->faker->text(),
                                 'faith' => $this->faker->text(),
                                 'events' => $this->faker->text(),
                                 'character' => $this->faker->text(),
                                 'death' => $this->faker->text(),
                                 'life' => $this->faker->text(),
                                 'attending' => join(', ', [$this->faker->name, $this->faker->name, $this->faker->name]
                                 ),
                                 'quotes' => $this->faker->text(),
                                 'spoken_name' => '',
                                 'professional_life' => $this->faker->text(),
                                 'birth_place' => $this->faker->city,
                                 'death_place' => $this->faker->city,
                                 'dimissorial_issuer' => 'Pfarramt ' . $this->faker->city,
                                 'birth_name' => $this->faker->lastName,
                                 'appointment_address' => $this->faker->address,
                             ]);
        }
        $this->info(count($funerals) . ' funerals created.');
    }

    protected function handleParishes()
    {
        $this->line('Creating parishes...');
        $parishes = Parish::with('owningCity')->get();
        /** @var Parish $parish */
        foreach ($parishes as $parish) {
            $name = str_replace('Pfarramt ', '', $parish->name);
            $parish->update([
                                'code' => trim('Pfarramt ' . $parish->owningCity->name . ' ' . $name),
                                'address' => $this->faker->streetAddress,
                                'zip' => $this->faker->postcode,
                                'city' => $parish->owningCity->name,
                                'phone' => $this->faker->phoneNumber,
                                'email' => $this->faker->email,
                            ]);
        }
        $this->info(count($parishes) . ' parishes created.');
    }

    protected function handleStreetRanges()
    {
        StreetRange::query()->delete();
        $this->info('All street ranges deleted');
    }

    protected function handleUsers()
    {
        $universalPassword = 'test';
        $this->line('Creating users...');
        $users = User::all();
        foreach ($users as $user) {
            if ($user->name != 'Admin') {
                $data = [
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'address' => $this->faker->address,
                    'phone' => $this->faker->phoneNumber,
                    'email' => strtolower('first_name . ' . ' . ' . $user->last_name) . '@demo.pfarrplaner.de',
                    'office' => '',
                    'api_token' => '',
                    'own_website' => $this->faker->url,
                    'own_podcast_title' => $this->faker->sentence,
                    'own_podcast_url' => $this->faker->url,
                    'own_podcast_spotify' => $this->faker->url,
                    'own_podcast_itunes' => $this->faker->url,
                ];
                if ($user->password != '') {
                    $data['password'] = $universalPassword;
                }
            } else {
                $data['password'] = 'admin';
            }
            $user->update($data);
            $user->calendarConnections()->delete();
        }
        $this->info(count($users) . ' users created.');
    }

    protected function handleWeddings()
    {
        $this->line('Creating weddings...');
        $weddings = Wedding::all();
        /** @var Wedding $wedding */
        foreach ($weddings as $wedding) {
            $wedding->update([
                                 'spouse1_name' => $this->faker->name('male'),
                                 'spouse1_phone' => $this->faker->phoneNumber,
                                 'spouse1_email' => $this->faker->email,
                                 'spouse1_birth_name' => $this->faker->lastName,
                                 'spouse2_name' => $this->faker->name('female'),
                                 'spouse2_phone' => $this->faker->phoneNumber,
                                 'spouse2_email' => $this->faker->email,
                                 'spouse2_birth_name' => $this->faker->lastName,
                                 'spouse1_dob' => $this->faker->date(),
                                 'spouse1_address' => $this->faker->streetAddress,
                                 'spouse1_zip' => $this->faker->postcode,
                                 'spouse1_city' => $this->faker->city,
                                 'spouse1_dimissorial_issuer' => 'Pfarramt ' . $this->faker->city,
                                 'spouse2_dob' => $this->faker->date(),
                                 'spouse2_address' => $this->faker->streetAddress,
                                 'spouse2_zip' => $this->faker->postcode,
                                 'spouse2_city' => $this->faker->city,
                                 'spouse2_dimissorial_issuer' => 'Pfarramt ' . $this->faker->city,
                                 'notes' => $this->faker->text(),
                                 'music' => $this->faker->text(),
                                 'gift' => $this->faker->text(),
                                 'flowers' => $this->faker->text(),
                             ]);
        }
        $this->info(count($weddings) . ' weddings created.');
    }

}
