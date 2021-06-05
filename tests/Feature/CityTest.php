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

use App\City;
use App\Http\Middleware\Authenticate;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Class CityTest
 * @package Tests\Feature
 */
class CityTest extends TestCase
{

    use RefreshDatabase;

    /** @var User */
    protected $user;

    /**
     * Test that the city index works correctly
     * @return void
     * @test
     */
    public function testCityIndexWorks()
    {
        $response = $this->actingAs($this->user)->get(route('cities.index'));
        $response->assertStatus(200)
            ->assertViewIs('cities.index');
    }

    /**
     * Test that the city create page works correctly
     * @return void
     * @test
     */
    public function testCityCreateWorks() {
        $response = $this->actingAs($this->user)->get(route('cities.create'));
        $response->assertStatus(200)
            ->assertViewIs('cities.create');

    }

    /**
     * Test that the city edit page works correctly
     * @return void
     * @test
     */
    public function testCityEditWorks() {
        $city = factory(City::class)->create();
        $response = $this->actingAs($this->user)->get(route('cities.edit', $city->id));
        $response->assertStatus(200)
            ->assertViewIs('cities.edit');

    }


    /**
     * Test that a city can be successfully added
     * @return void
     * @test
     */
    public function testACityCanBeAdded()
    {
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
        $response = $this->post(
            route('cities.store'),
            factory(City::class)->raw(
                [
                    'name' => '',
                ]
            )
        );

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
        $response = $this->post(
            route('cities.store'),
            factory(City::class)->raw(
                [
                    'name' => Str::random(256),
                ]
            )
        );

        $response->assertSessionHasErrors('name');
        $this->assertCount(0, City::all());
    }

    /**
     * Test that a city can be updated
     * @return void
     * @test
     */
    public function testCityCanBeUpdated()
    {
        $city = factory(City::class)->create(['name' => 'Stuttgart']);

        $this->assertTrue($city->update(['name' => 'New York']));

        $response = $this->patch(
            route('cities.update', $city->id),
            [
                'name' => 'Hamburg',
            ]
        );

        $response->assertStatus(302);
        $this->assertEquals('Hamburg', City::find($city->id)->name);
    }

    /**
     * Test that a city can be deleted
     * @return void
     * @test
     */
    public function testCityCanBeDeleted()
    {
        $city = factory(City::class)->create();
        $response = $this->delete(route('cities.destroy', $city->id));
        $response->assertStatus(302);
        $this->assertCount(0, City::all());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([Authenticate::class]);

        Storage::fake('fake');

        Permission::create(['name' => 'benutzerliste-lokal-sehen']);
        Permission::create(['name' => 'rollen-bearbeiten']);
        Permission::create(['name' => 'pfarramt-bearbeiten']);

        $this->user = factory(User::class)->create();
        $this->user->givePermissionTo('benutzerliste-lokal-sehen');
        $this->user->givePermissionTo('rollen-bearbeiten');
        $this->user->givePermissionTo('pfarramt-bearbeiten');
    }


}
