<?php

namespace FriendsOfCat\Tests\LaravelDbMaintenance;

use FriendsOfCat\LaravelDbMaintenance\Maintenance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

/**
 * @coversDefaultClass \FriendsOfCat\LaravelDbMaintenance\Maintenance
 */
class MaintenanceTest extends TestCase
{

    use RefreshDatabase;

    public function testTogglingMaintenance()
    {
        /** @var Maintenance $maintenance */
        $maintenance = $this->app->make(Maintenance::class);

        $this->assertTrue($maintenance->isUp());
        $this->assertFalse($maintenance->isDown());

        // False, as already up.
        $this->assertFalse($maintenance->up());

        $this->assertDatabaseMissing('maintenance', ['status' => 1]);

        // Down
        $this->assertTrue($maintenance->down());

        $this->assertTrue($maintenance->isDown());
        $this->assertFalse($maintenance->isUp());

        // False, as already down.
        $this->assertFalse($maintenance->down());

        $this->assertDatabaseHas('maintenance', ['status' => 1]);

        // Back up.
        $this->assertTrue($maintenance->up());

        $this->assertTrue($maintenance->isUp());
        $this->assertFalse($maintenance->isDown());

        $this->assertDatabaseMissing('maintenance', ['status' => 1]);

        // Do the process again to make sure there is only one entry in the DB
        // that qualifies.
        $this->assertTrue($maintenance->down());
        $this->assertTrue($maintenance->isDown());

        $this->assertDatabaseHas('maintenance', ['status' => 1]);
        $this->assertEquals(1, DB::table('maintenance')->where('status', true)->count());

        $this->assertTrue($maintenance->up());
        $this->assertTrue($maintenance->isUp());

        $this->assertDatabaseMissing('maintenance', ['status' => 1]);
        $this->assertEquals(2, DB::table('maintenance')->where('status', false)->count());
    }
}
