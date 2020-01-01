<?php

namespace Tests\Feature;

use App\City;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CityTest extends TestCase
{

    use RefreshDatabase, WithoutMiddleware;


    /**
     * Test that a city can be successfully added
     * @return void
     * @test
     */
    public function testACityCanBeAdded()
    {

        $this->withoutExceptionHandling();

        $response = $this->post(route('cities.store'), factory(City::class)->raw());

        $response->assertStatus(302);
        $this->assertCount(1, City::all());
    }

    /**
     * Test that a city cannot be created without name
     * @return void
     * @test
     */
    public function testCityNeedsName()
    {
        $response = $this->post(route('cities.store'), factory(City::class)->raw([
            'name' => '',
        ]));

        $response->assertSessionHasErrors('name');
        $this->assertCount(0, City::all());
    }

    /**
     * Test that a city cannot be created without name
     * @return void
     * @test
     */
    public function testCityNeedsNameShorterThan255()
    {
        $response = $this->post(route('cities.store'), factory(City::class)->raw([
            'name' => str_random(256),
        ]));

        $response->assertSessionHasErrors('name');
        $this->assertCount(0, City::all());
    }

    /**
     * Test that a city can be updated
     * @return void
     * @test
     */
    public function testCityCanBeUpdated() {
        $city = factory(City::class)->create(['name' => 'Stuttgart']);
        $response = $this->patch(route('cities.update', $city->id), [
            'name' => 'München',
        ]);

        $response->assertStatus(302);
        $this->assertEquals('München', City::first()->name);

    }

    /**
     * Test that a city can be deleted
     * @return void
     * @test
     */
    public function testCityCanBeDeleted() {
        $city = factory(City::class)->create();
        $response = $this->delete(route('cities.destroy', $city->id));
        $response->assertStatus(302);
        $this->assertCount(0, City::all());
    }


}
