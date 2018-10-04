<?php

namespace FriendsOfCat\Tests\LaravelDbMaintenance\Console;

use FriendsOfCat\LaravelDbMaintenance\Maintenance;
use FriendsOfCat\Tests\LaravelDbMaintenance\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @coversDefaultClass \FriendsOfCat\LaravelDbMaintenance\Console\DownCommand
 */
class DownCommandTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @covers ::handle
     */
    public function testHandle()
    {
        $this->withoutMockingConsoleOutput();

        $this->artisan('down');

        $maintenance = $this->app->make(Maintenance::class);
        $this->assertTrue($maintenance->isDown());
    }
}
