<?php

namespace App\Console\Commands;

use App\City;
use App\User;
use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

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
        $dbName = Config::get('database.connections.'.Config::get('database.default').'.database');
        $this->line('Demo builder, using database "'.$dbName.'"');
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
        return;
        die();
        $faker = Factory::create('de_DE');


        $this->line('Creating cities...');
        $cities = City::all();
        foreach ($cities as $city) {
            $city->name = $faker->city;
            $city->save();
        }
        $this->info(count($city).' cities created.');

        $this->line('Creating users...');
        $users = User::all();
        foreach ($users as $user) {
            if ($user->name != 'Admin') {
                $user->first_name = $faker->firstName;
                $user->last_name = $faker->lastName;
                $user->name = $user->first_name.' '.$user->last_name;
                $user->address = $faker->address;
                $user->phone = $faker->phone;
            }
            $user->email = strtolower($user->first_name.'.'.$user->last_name).'@demo.pfarrplaner.de';
            $user->save();
        }
        $this->info(count($users).' users created.');
    }
}
