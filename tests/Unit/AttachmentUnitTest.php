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

namespace Tests\Unit;

use App\Attachment;
use App\Funeral;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class AttachmentUnitTest
 * @package Tests\Unit
 */
class AttachmentUnitTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Test that an attachment can be created
     *
     * @return void
     */
    public function testAttachmentCanBeCreated()
    {
        $this->withoutExceptionHandling();
        $attachment = Attachment::create(['title' => 'cool title', 'file' => 'file.txt']);
        $this->assertCount(1, Attachment::all());
    }

    /**
     * Test that an attachment can be created and attached to a funeral
     *
     * @return void
     */
    public function testAttachmentCanBeCreatedForFuneral()
    {
        $this->withoutExceptionHandling();
        $funeral = factory(Funeral::class)->create();
        $this->assertCount(1, Funeral::all());
        $funeral->attachments()->create(['title' => 'cool title', 'file' => 'file.txt']);
        $this->assertCount(1, Funeral::first()->attachments);
    }
}
