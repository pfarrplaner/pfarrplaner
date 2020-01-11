<?php

namespace Tests\Feature;

use App\Funeral;
use App\Traits\TestWithCredentialsTrait;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        $raw = factory(Funeral::class)->raw();

        $this->actingAs($this->user)
            ->post(route('funerals.store'), $raw)
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
