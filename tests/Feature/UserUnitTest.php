<?php

namespace Tests\Feature;

use App\HomeScreens\PastorHomeScreen;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserUnitTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Test if getting a HomeScreenObject works
     *
     * @return void
     */
    public function testGetHomeScreen()
    {
        $user = factory(User::class)->create();
        $this->assertNull($user->getHomeScreen());
        $user->setSetting('homeScreen', 'homescreen:pastor');
        $this->assertInstanceOf(PastorHomeScreen::class, $user->getHomeScreen());
    }
}
