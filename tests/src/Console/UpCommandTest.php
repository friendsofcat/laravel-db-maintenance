<?php

namespace FriendsOfCat\Tests\LaravelDbMaintenance\Console;

use FriendsOfCat\LaravelDbMaintenance\Maintenance;
use FriendsOfCat\Tests\LaravelDbMaintenance\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @coversDefaultClass \FriendsOfCat\LaravelDbMaintenance\Console\UpCommand
 */
class UpCommandTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @covers ::handle
     */
    public function testHandle()
    {
        $this->withoutMockingConsoleOutput();

        $maintenance = $this->app->make(Maintenance::class);
        $maintenance->down();

        $this->artisan('up');

        $this->assertTrue($maintenance->isUp());
    }
}
