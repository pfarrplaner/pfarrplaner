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
}
