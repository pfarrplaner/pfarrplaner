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

namespace Tests\Unit;

use App\Tag;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagUnitTest extends TestCase
{

    use RefreshDatabase, WithFaker;


    /**
     * Test if a tag can be created in the database
     *
     * @return void
     */
    public function testTagCanBeCreated()
    {
        Tag::create(factory(Tag::class)->raw());
        $this->assertCount(1, Tag::all());
    }

    /**
     * Test if a tag can be updated in the database
     *
     * @return void
     */
    public function testTagCanBeUpdated()
    {
        $tag = Tag::create(factory(Tag::class)->raw());
        $code2 = Str::random(20);
        $this->assertEquals($tag->code, Tag::first()->code);
        $tag->update(['code' => $code2]);
        $this->assertEquals($code2, Tag::first()->code);
    }


    /**
     * Test if a tag can be deleted from the database
     *
     * @return void
     */
    public function testTagCanBeDeleted() {
        $tag = Tag::create(factory(Tag::class)->raw());
        $this->assertCount(1, Tag::all());
        $tag->delete();
        $this->assertCount(0, Tag::all());
    }


}
