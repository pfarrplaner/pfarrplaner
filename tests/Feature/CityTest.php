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

        $response = $this->post(route('cities.store'), [
            'name' => 'Stuttgart',
            'public_events_calendar_url' => '',
            'default_offering_goal' => '',
            'default_offering_description' => '',
            'default_funeral_offering_goal' => '',
            'default_funeral_offering_description' => '',
            'default_wedding_offering_goal' => '',
            'default_wedding_offering_description' => '',
            'op_domain' => '',
            'op_customer_key' => '',
            'op_customer_token' => '',
        ]);

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
        $response = $this->post(route('cities.store'), [
            'name' => '',
            'public_events_calendar_url' => '',
            'default_offering_goal' => '',
            'default_offering_description' => '',
            'default_funeral_offering_goal' => '',
            'default_funeral_offering_description' => '',
            'default_wedding_offering_goal' => '',
            'default_wedding_offering_description' => '',
            'op_domain' => '',
            'op_customer_key' => '',
            'op_customer_token' => '',
        ]);

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
        $response = $this->post(route('cities.store'), [
            'name' => str_random(256),
            'public_events_calendar_url' => '',
            'default_offering_goal' => '',
            'default_offering_description' => '',
            'default_funeral_offering_goal' => '',
            'default_funeral_offering_description' => '',
            'default_wedding_offering_goal' => '',
            'default_wedding_offering_description' => '',
            'op_domain' => '',
            'op_customer_key' => '',
            'op_customer_token' => '',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertCount(0, City::all());
    }

    /**
     * Test that a city can be updated
     * @return void
     * @test
     */
    public function testCityCanBeUpdated() {
        $this->post(route('cities.store'), [
            'name' => 'Stuttgart',
        ]);
        $city = City::first();
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
        $this->post(route('cities.store'), [
            'name' => 'Stuttgart',
        ]);
        $response = $this->delete(route('cities.destroy', City::first()->id));
        $response->assertStatus(302);
        $this->assertCount(0, City::all());
    }


}
