<?php

namespace Tests\Feature;

use App\City;
use App\Http\Middleware\Authenticate;
use App\Service;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceFeatureTest extends TestCase
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
     * Test that a service can be created
     *
     * @return void
     */
    public function testServiceCanBeCreated()
    {
        $response = $this->actingAs($this->user)
            ->post(route('services.store'), factory(Service::class)->raw());
        $response->assertStatus(302);
        $this->assertCount(1, Service::all());
    }

    /**
     * Test that a service can be updated
     *
     * @return void
     */
    public function testServiceCanBeUpdated()
    {
        $this->actingAs($this->user)
            ->post(route('services.store'), factory(Service::class)->raw(['city_id' => $this->city]));
        $response = $this->actingAs($this->user)
            ->patch(
                route('services.update', Service::first()->id),
                factory(Service::class)->raw(['description' => 'cool title'])
            );
        $response->assertStatus(302);
        $this->assertEquals('cool title', Service::first()->description);
    }


    /**
     * Test that a service cannot be updated without write permissions for city
     *
     * @return void
     */
    public function testServiceCannotBeUpdatedForCityWithoutWritePermissions()
    {
        $city = factory(City::class)->create();
        $service = factory(Service::class)->create(['city_id' => $city]);
        $title = $service->description;
        $response = $this->actingAs($this->user)
            ->patch(
                route('services.update', Service::first()->id),
                factory(Service::class)->raw(['description' => 'cool title'])
            );
        $response->assertStatus(403);
        $this->assertEquals($title, Service::first()->description);
    }

    /**
     * Test that a service can be updated
     *
     * @return void
     */
    public function testServiceCanBeDeleted()
    {
        $service = factory(Service::class)->create(['city_id' => $this->city]);
        $this->assertCount(1, Service::all());
        $response = $this->actingAs($this->user)
            ->delete(route('services.destroy', $service->id));
        $response->assertStatus(302);
        $this->assertCount(0, Service::all());
    }

    public function testCheckBoxesCanBeSetAndUnset() {
        $this->withoutExceptionHandling();
        $serviceData = factory(Service::class)->raw(['city_id' => $this->city, 'need_predicant' => 1]);
        $this->actingAs($this->user)->post(route('services.store'), $serviceData)->assertStatus(302);
        $this->assertCount(1, Service::all());
        $service = Service::first();
        unset($serviceData['need_predicant']);
        $this->actingAs($this->user)->patch(route('services.update', $service->id), $serviceData)->assertStatus(302);
        $this->assertEquals(0, Service::first()->need_predicant);
    }
}
