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
use App\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class TagFeatureTest
 * @package Tests\Feature
 */
class TagFeatureTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Test if a tag can be created
     *
     * @return void
     */
    public function testTagCanBeCreated()
    {
        $response = $this->post(route('tags.store'), factory(Tag::class)->raw());
        $response->assertStatus(302);
        $response->assertRedirect(route('tags.index'));
        $this->assertCount(1, Tag::all());
    }

    /**
     * Test that a tag cannot be created without a name
     *
     * @return void
     */
    public function testTagCannotBeCreatedWithoutName()
    {
        $response = $this->post(route('tags.store'), factory(Tag::class)->raw(['name' => null]));
        $response->assertSessionHasErrors('name');
        $this->assertCount(0, Tag::all());
    }

    /**
     * Test if a tag can be updated
     *
     * @return void
     */
    public function testTagCanBeUpdated()
    {
        $tag = factory(Tag::class)->create();
        $response = $this->patch(route('tags.update', $tag->id), ['name' => 'cool name']);
        $response->assertStatus(302);
        $response->assertRedirect(route('tags.index'));
        $this->assertEquals('cool name', Tag::first()->name);
    }

    /**
     * Test that a tag cannot be updated without a name
     *
     * @return void
     */
    public function testTagCannotBeUpdatedWithoutName()
    {
        $tag = factory(Tag::class)->create(['name' => 'cool name']);
        $response = $this->patch(route('tags.update', $tag->id), ['name' => null]);
        $response->assertSessionHasErrors('name');
        $this->assertEquals('cool name', Tag::first()->name);
    }

    /**
     * Test that a tag's code is auto-created as a slug of the title
     *
     * @return void
     */
    public function testTagCodeIsSlug()
    {
        $this->post(route('tags.store'), factory(Tag::class)->raw(['code' => null]));
        $tag = Tag::first();
        $this->assertEquals(Str::slug($tag->name), $tag->code);
    }

    /**
     * Test that a tag can be deleted
     *
     * @return void
     */
    public function testTagCanBeDeleted()
    {
        $tag = factory(Tag::class)->create();
        $this->assertCount(1, Tag::all());
        $response = $this->delete(route('tags.destroy', $tag->id));
        $response->assertStatus(302);
        $response->assertRedirect(route('tags.index'));
        $this->assertCount(0, Tag::all());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(Authenticate::class);
    }
}
