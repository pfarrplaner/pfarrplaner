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

use App\Attachment;
use App\Funeral;
use App\Traits\TestWithCredentialsTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Shetabit\Visitor\Middlewares\LogVisits;
use Tests\TestCase;

/**
 * Class AttachmentFeatureTest
 * @package Tests\Feature
 */
class AttachmentFeatureTest extends TestCase
{

    use RefreshDatabase;
    use TestWithCredentialsTrait {
        setUp as traitSetUp;
    }

    /**
     * Test that a funeral can be created with an attachment
     *
     * @return void
     */
    public function testFuneralAttachmentCanBeCreated()
    {
        $funeral = factory(Funeral::class)->create();
        $this->actingAs($this->user)
            ->post(route('funeral.attach', $funeral->id), $this->fakeFuneralAttachmentData())
            ->assertStatus(200)
            ->assertJson([]);

        $this->assertCount(1, Funeral::all());
        $this->assertCount(1, Funeral::first()->attachments);
    }

    /**
     * Test that an attachment can be removed from a funeral
     *
     * @return void
     */
    public function testFuneralAttachmentCanBeRemoved()
    {
        $funeral = factory(Funeral::class)->create();
        $this->actingAs($this->user)
            ->post(route('funeral.attach', $funeral->id), $this->fakeFuneralAttachmentData())
            ->assertStatus(200)
            ->assertJson([]);

        $this->assertCount(1, Funeral::all());
        $funeral = Funeral::first();
        $this->assertCount(1, $funeral->attachments);
        $this->actingAs($this->user)
            ->delete(route('funeral.detach', ['funeral' => $funeral->id, 'attachment' => $funeral->attachments[0]->id]))
            ->assertStatus(200)
            ->assertJson([]);
        $this->assertCount(0, Funeral::first()->attachments);
    }

    /**
     * Prepare data for fake attachment
     * @return array
     */
    protected function fakeFuneralAttachmentData(): array
    {
        Storage::fake('fake');
        $raw['attachments'][1] = UploadedFile::fake()->image('test.jpg');
        $raw['attachment_text'][1] = 'Testing';
        return $raw;
    }


    /**
     * Setup test
     */
    protected function setUp(): void
    {
        $this->traitSetUp();
        $this->withoutMiddleware([LogVisits::class]);
    }
}
