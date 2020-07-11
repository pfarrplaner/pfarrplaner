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

namespace Tests\Feature;

use App\City;
use App\Http\Middleware\Authenticate;
use App\Service;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Class ServiceFeatureTest
 * @package Tests\Feature
 */
class ServiceFeatureTest extends TestCase
{

    use RefreshDatabase;

    /** @var City */
    protected $city;

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

    public function testCheckBoxesCanBeSetAndUnset()
    {
        $this->withoutExceptionHandling();
        $serviceData = factory(Service::class)->raw(['city_id' => $this->city, 'need_predicant' => 1]);
        $this->actingAs($this->user)->post(route('services.store'), $serviceData)->assertStatus(302);
        $this->assertCount(1, Service::all());
        $service = Service::first();
        unset($serviceData['need_predicant']);
        $this->actingAs($this->user)->patch(route('services.update', $service->id), $serviceData)->assertStatus(302);
        $this->assertEquals(0, Service::first()->need_predicant);
    }

    /**
     * Test that a service can have a title
     *
     * @return void
     * @test
     */
    public function testServiceCanHaveTitle()
    {
        $this->withoutExceptionHandling();
        $data = factory(Service::class)->raw();
        $data['title'] = 'Cool title';
        $this->actingAs($this->user)
            ->post(route('services.store'), $data)
            ->assertStatus(302);
        $this->assertEquals('Cool title', Service::first()->title);
    }

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
