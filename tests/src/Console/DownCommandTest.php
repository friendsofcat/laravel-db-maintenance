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

        $this->assertDatabaseHas('maintenance', [
            'status' => true,
            'retry_after' => 60,
        ]);
    }

    /**
     * @covers ::handle
     */
    public function testHandleWithOptions()
    {
        $this->withoutMockingConsoleOutput();

        $test_message = 'This is a message parameter';

        $this->artisan('down', ['--retry' => 99, '--message' => $test_message]);

        $maintenance = $this->app->make(Maintenance::class);
        $this->assertTrue($maintenance->isDown());

        $latest = $maintenance->getLatest();

        $this->assertEquals(1, $latest->status);
        $this->assertEquals(99, $latest->retry_after);
        $this->assertSame($test_message, $latest->message);

        $this->assertDatabaseHas('maintenance', [
            'id' => $latest->id,
            'status' => true,
            'retry_after' => 99,
            'message' => $test_message
        ]);
    }
}
