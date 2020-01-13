<?php

namespace Tests\Unit;

use App\Attachment;
use App\Funeral;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
