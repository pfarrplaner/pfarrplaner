<?php

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
