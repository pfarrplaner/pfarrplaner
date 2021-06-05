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

use App\Funeral;
use App\Service;
use App\Traits\TestWithCredentialsTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class FuneralFeatureTest
 * @package Tests\Feature
 */
class FuneralFeatureTest extends TestCase
{
    use RefreshDatabase;
    use TestWithCredentialsTrait;

    /**
     * Test that a funeral can be created
     *
     * @return void
     */
    public function testFuneralCanBeCreated()
    {
        $service = factory(Service::class)->create();
        $this->actingAs($this->user)
            ->get(route('funerals.create', $service->id))
            ->assertStatus(302);
        $this->assertCount(1, Funeral::all());
    }

    /**
     * Test that a funeral can be updated
     *
     * @return void
     */
    public function testFuneralCanBeUpdated()
    {
        $this->withoutExceptionHandling();
        $raw = factory(Funeral::class)->raw();
        $funeral = Funeral::create($raw);
        $this->assertCount(1, Funeral::all());
        $raw['buried_name'] = 'Karl Otto';
        $this->actingAs($this->user)
            ->patch(route('funerals.update', $funeral->id), $raw)
            ->assertStatus(302);
        $this->assertCount(1, Funeral::all());
        $this->assertEquals('Karl Otto', Funeral::first()->buried_name);
    }
}
