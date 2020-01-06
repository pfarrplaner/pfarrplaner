<?php

namespace Tests\Feature;

use App\Http\Middleware\Authenticate;
use App\Tag;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagFeatureTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();
        $this->withoutMiddleware(Authenticate::class);
    }


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
    public function testTagCodeIsSlug() {
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
}
