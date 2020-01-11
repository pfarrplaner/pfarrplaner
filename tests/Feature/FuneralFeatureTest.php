<?php

namespace Tests\Feature;

use App\City;
use App\Funeral;
use App\Http\Middleware\Authenticate;
use App\User;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FuneralFeatureTest extends TestCase
{
    use RefreshDatabase;

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

    /**
     * Test that a funeral can be created
     *
     * @return void
     */
    public function testFuneralCanBeCreated()
    {
        $this->withoutExceptionHandling();
        $raw = factory(Funeral::class)->raw();

        $this->actingAs($this->user)
            ->post(route('funerals.store'), $raw)
            ->assertStatus(302);
        $this->assertCount(1, Funeral::all());
    }
}
