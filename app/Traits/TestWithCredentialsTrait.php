<?php


namespace App\Traits;


use App\City;
use App\Http\Middleware\Authenticate;
use App\User;
use Spatie\Permission\Models\Permission;

trait TestWithCredentialsTrait
{

    /** @var City */
    protected $city;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(Authenticate::class);
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->registerPermissions();

        Permission::create(['name' => 'gd-bearbeiten']);

        $this->city = factory(City::class)->create();

        /** @var User */
        $this->user = factory(User::class)->create();
        $this->user->givePermissionTo('gd-bearbeiten');
        $this->user->writableCities()->attach($this->city);
    }


}
