<?php

namespace Tests\Feature;

use App\City;
use App\Http\Middleware\Authenticate;
use App\Location;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocationTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();
        $this->withoutMiddleware([Authenticate::class]);
    }



    /**
     * Test if a location can be created
     * @return void
     * @test
     */
    public function testLocationCanBeCreated()
    {
        $response = $this->post(route('locations.store'), factory(Location::class)->raw());

        $response->assertStatus(302);
        $this->assertCount(1, Location::all());
    }


    /**
     * Test if a location can be created without a name
     * @return void
     * @test
     */
    public function testLocationNeedsName()
    {
        $response = $this->post(route('locations.store'), factory(Location::class)->raw(['name' => '']));
        $response->assertSessionHasErrors('name');
        $this->assertCount(0, Location::all());
    }

    /**
     * Test if a location can be created without a city id
     * @return void
     * @test
     */
    public function testLocationNeedsCity()
    {
        $response = $this->post(route('locations.store'), factory(Location::class)->raw(['city_id' => null]));
        $response->assertSessionHasErrors('city_id');
        $this->assertCount(0, Location::all());
    }

    /**
     * Test if a location can be created with an invalid default time
     * @return void
     * @test
     */
    public function testLocationNeedsValidDefaultTime()
    {
        $response = $this->post(route('locations.store'), factory(Location::class)->raw(['default_time' => str_random(5)]));
        $response->assertSessionHasErrors('default_time');
        $this->assertCount(0, Location::all());
    }

    /**
     * Test if a location can be updated
     * @return void
     * @test
     */
    public function testLocationCanBeUpdated()
    {
        $location = factory(Location::class)->create(['name' => 'Pauluskirche']);
        $response = $this->patch(route('locations.update', $location->id), factory(Location::class)->raw(['name' => 'Peterskirche']));
        $response->assertStatus(302);
        $this->assertEquals('Peterskirche', Location::first()->name);
    }


    /**
     * Test if a location can be updated
     * @return void
     * @test
     */
    public function testLocationCanBeDeleted() {
        $location = factory(Location::class)->create();
        $this->assertTrue($location->exists);
        $response = $this->delete(route('locations.destroy', $location->id));
        $response->assertStatus(302);
        $this->assertCount(0, Location::all());
    }

}
