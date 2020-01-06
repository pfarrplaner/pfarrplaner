<?php

namespace Tests\Unit;

use App\Service;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceUnitTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Test that a service can be created
     *
     * @return void
     */
    public function testServiceCanBeCreated()
    {
        factory(Service::class)->create();
        $this->assertCount(1, Service::all());
    }

    /**
     * Test that a service can be updated
     *
     * @return void
     */
    public function testServiceCanBeUpdated()
    {
        $service = factory(Service::class)->create();
        $this->assertCount(1, Service::all());
        $service->update(['description' => 'cool title']);
        $this->assertEquals('cool title', Service::first()->description);
    }

    /**
     * Test that a service can be deleted
     *
     * @return void
     */
    public function testServiceCanBeDeleted()
    {
        $service = factory(Service::class)->create();
        $this->assertCount(1, Service::all());
        $service->delete();
        $this->assertCount(0, Service::all());
    }



}
