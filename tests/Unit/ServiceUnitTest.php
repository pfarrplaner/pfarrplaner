<?php

namespace Tests\Unit;

use App\Http\Requests\StoreServiceRequest;
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
     * @test
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
     * @test
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
     * @test
     */
    public function testServiceCanBeDeleted()
    {
        $service = factory(Service::class)->create();
        $this->assertCount(1, Service::all());
        $service->delete();
        $this->assertCount(0, Service::all());
    }

    /**
     * Test that checkbox fields can be set and unset
     *
     * @return void
     * @test
     */
    public function testCheckBoxesCanBeSetAndUnset() {
        $service = factory(Service::class)->raw(['need_predicant' => 1]);
        $rules = (new StoreServiceRequest())->rules();
        $validator = app('validator')->make($service, $rules);
        $this->assertTrue($validator->passes());
        $data = $validator->validate();
        $this->assertEquals(1, $data['need_predicant']);
        unset($service['need_predicant']);
        $validator = app('validator')->make($service, $rules);
        $data = $validator->validate();
        $this->assertEquals(0, $data['need_predicant']);
    }


    /**
     * Test that a service can have a title
     *
     * @return void
     * @test
     */
    public function testServiceCanHaveTitle()
    {
        $data = factory(Service::class)->raw();
        $data['title'] = 'Cool title';
        $service = Service::create($data);
        $this->assertEquals('Cool title', Service::first()->title);
    }


}
