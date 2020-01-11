<?php

namespace Tests\Feature;

use App\Attachment;
use App\Funeral;
use App\Traits\TestWithCredentialsTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Shetabit\Visitor\Middlewares\LogVisits;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttachmentFeatureTest extends TestCase
{

    use RefreshDatabase;
    use TestWithCredentialsTrait {
        setUp as traitSetUp;
    }

    protected function setUp(): void
    {
        $this->traitSetUp();
        $this->withoutMiddleware([LogVisits::class]);
    }


    /**
     * Test that a funeral can be created with an attachment
     *
     * @return void
     */
    public function testFuneralAttachmentCanBeCreated()
    {
        $this->withoutExceptionHandling();
        Storage::fake('funerals');
        $raw = factory(Funeral::class)->raw();
        $raw['attachments'][] = UploadedFile::fake()->image('test.jpg');

        $this->actingAs($this->user)
            ->post(route('funerals.store'), $raw)
            ->assertStatus(302);

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
        $this->withoutExceptionHandling();
        Storage::fake('funerals');
        $raw = factory(Funeral::class)->raw();
        $raw['attachments'][] = UploadedFile::fake()->image('test.jpg');

        $this->actingAs($this->user)
            ->post(route('funerals.store'), $raw)
            ->assertStatus(302);

        $this->assertCount(1, Funeral::all());
        $this->assertCount(1, Funeral::first()->attachments);

        unset($raw['attachments']);
        $raw['remove_attachment'][] = Attachment::first()->id;
        $this->actingAs($this->user)
            ->patch(route('funerals.update', Funeral::first()->id), $raw)
            ->assertStatus(302);

        $this->assertCount(0, Funeral::first()->attachments);
    }
}
