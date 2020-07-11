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
use App\Day;
use App\Http\Middleware\Authenticate;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DayTest
 * @package Tests\Feature
 */
class DayTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Test if a day can be created
     * @return void
     * @test
     */
    public function testDayCanBeCreated()
    {
        $data = factory(Day::class)->raw(['day_type' => 0]);
        $response = $this->post(route('days.store'), $data);

        $response->assertStatus(302);
        $this->assertCount(1, Day::all());
        $this->assertInstanceOf(Carbon::class, Day::first()->date);
    }

    /**
     * Test if a day of type DAY_LIMITED can be created
     * @return void
     * @test
     */
    public function testLimitedDayCanBeCreated()
    {
        $data = factory(Day::class)->raw(['day_type' => 1]);
        $data['cities'] = [factory(City::class)->create()->id];
        $response = $this->post(route('days.store'), $data);

        $response->assertStatus(302);
        $this->assertCount(1, Day::all());
    }

    /**
     * Test if a day of type DAY_LIMITED cannot be created without cities
     * @return void
     * @test
     */
    public function testLimitedDayCannotBeCreatedWithoutCities()
    {
        $data = factory(Day::class)->raw(['day_type' => 1]);
        $response = $this->post(route('days.store'), $data);

        $response->assertSessionHasErrors('cities');
        $this->assertCount(0, Day::all());
    }

    /**
     * Test if a day can't be created without date
     * @return void
     * @test
     */
    public function testDayCannotBeCreatedWithoutDate()
    {
        $response = $this->post(route('days.store'), factory(Day::class)->raw(['date' => null]));
        $response->assertSessionHasErrors('date');
        $this->assertCount(0, Day::all());
    }

    /**
     * Test if a day can't be created without correct date format
     * @return void
     * @test
     */
    public function testDayCannotBeCreatedWithoutCorrectDateFormat()
    {
        $response = $this->post(route('days.store'), factory(Day::class)->raw(['date' => str_random(5)]));
        $response->assertSessionHasErrors('date');
        $this->assertCount(0, Day::all());
    }

    /**
     * Test if a day can't be created with an invalid day_type
     * @return void
     * @test
     */
    public function testDayCannotBeCreatedWithInvalidDayType()
    {
        $response = $this->post(
            route('days.store'),
            factory(Day::class)->raw(['day_type' => 5, 'date' => '01.01.1990'])
        );
        $response->assertSessionHasErrors('day_type');
        $this->assertCount(0, Day::all());
    }

    /**
     * Test if a day can be updated with an invalid day_type
     * @return void
     * @test
     */
    public function testDayCanBeUpdated()
    {
        $day = factory(Day::class)->create(['day_type' => 0]);
        $response = $this->patch(
            route('days.update', $day->id),
            [
                'date' => '01.01.1990',
                'name' => 'test',
            ]
        );
        $response->assertStatus(302);
        $this->assertEquals('test', Day::find($day->id)->name);
    }

    /**
     * Test if a day can be updated with more cities
     * @return void
     * @test
     */
    public function testDayCanBeUpdatedWithMoreCities()
    {
        $city1 = factory(City::class)->create()->id;
        $city2 = factory(City::class)->create()->id;
        $data = factory(Day::class)->raw(['day_type' => 1]);
        $day = Day::create($data);
        $day->cities()->attach($city1);
        $response = $this->patch(
            route('days.update', $day->id),
            [
                'date' => '01.01.1990',
                'name' => 'test',
                'cities' => [$city1, $city2]
            ]
        );
        $response->assertStatus(302);

        $day = Day::find($day->id);
        $this->assertTrue($day->cities->pluck('id')->contains($city2));
    }

    /**
     * Test if a limited day is deleted when no more cities are attached
     * @return void
     * @test
     */
    public function testLimitedDayIsDeletedWhenNoMoreCitiesAreAttached()
    {
        $city1 = factory(City::class)->create()->id;
        $city2 = factory(City::class)->create()->id;
        $data = factory(Day::class)->raw(['day_type' => 1]);
        $day = Day::create($data);
        $day->cities()->attach([$city1, $city2]);
        $response = $this->patch(
            route('days.update', $day->id),
            [
                'date' => '01.01.1990',
                'day_type' => 1,
                'name' => 'test',
                'cities' => []
            ]
        );
        $response->assertStatus(302);
        $this->assertCount(0, Day::all());
    }

    /**
     * Test if a day can be deleted
     * @return void
     * @test
     */
    public function testDayCanBeDeleted()
    {
        $day = factory(Day::class)->create(['day_type' => 0]);
        $response = $this->delete(route('days.destroy', $day->id));
        $response->assertStatus(302);
        $this->assertCount(0, Day::all());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([Authenticate::class]);
    }

}
