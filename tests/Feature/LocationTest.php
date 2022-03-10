<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
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

namespace Tests\Feature;

use App\Http\Middleware\Authenticate;
use App\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class LocationTest
 * @package Tests\Feature
 */
class LocationTest extends TestCase
{

    use RefreshDatabase;

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
        $response = $this->post(
            route('locations.store'),
            factory(Location::class)->raw(['default_time' => Str::random(5)])
        );
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
        $response = $this->patch(
            route('location.update', $location->id),
            factory(Location::class)->raw(['name' => 'Peterskirche'])
        );
        $response->assertStatus(302);
        $this->assertEquals('Peterskirche', Location::first()->name);
    }

    /**
     * Test if a location can be updated
     * @return void
     * @test
     */
    public function testLocationCanBeDeleted()
    {
        $location = factory(Location::class)->create();
        $this->assertTrue($location->exists);
        $response = $this->delete(route('location.destroy', $location->id));
        $response->assertStatus(302);
        $this->assertCount(0, Location::all());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([Authenticate::class]);
    }

}
