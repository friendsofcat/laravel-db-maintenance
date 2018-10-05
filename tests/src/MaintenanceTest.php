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

    /**
     * @covers ::up
     * @covers ::isUp
     * @covers ::down
     * @covers ::isDown
     * @covers ::getLatest
     */
    public function testTogglingMaintenance()
    {
        /** @var Maintenance $maintenance */
        $maintenance = $this->app->make(Maintenance::class);

        $this->assertTrue($maintenance->isUp());
        $this->assertFalse($maintenance->isDown());

        // False, as already up.
        $this->assertFalse($maintenance->up());

        $this->assertDatabaseMissing('maintenance', []);

        // Down
        $this->assertTrue($maintenance->down());

        $this->assertTrue($maintenance->isDown());
        $this->assertFalse($maintenance->isUp());

        // False, as already down.
        $this->assertFalse($maintenance->down());

        $this->assertDatabaseHas('maintenance', ['status' => 1]);
        $this->assertMaintenanceTableCount(1, true);
        $this->assertMaintenanceTableCount(1);

        // Back up.
        $this->assertTrue($maintenance->up());

        $this->assertTrue($maintenance->isUp());
        $this->assertFalse($maintenance->isDown());

        $this->assertDatabaseHas('maintenance', ['status' => 0]);
        $this->assertMaintenanceTableCount(1, false);
        $this->assertMaintenanceTableCount(1);

        // Do the process again to make sure there is only one entry in the DB
        // that qualifies.
        $this->assertTrue($maintenance->down());
        $this->assertTrue($maintenance->isDown());

        $this->assertDatabaseHas('maintenance', ['status' => 1]);
        $this->assertMaintenanceTableCount(1, true);

        $this->assertTrue($maintenance->up());
        $this->assertTrue($maintenance->isUp());

        $this->assertDatabaseHas('maintenance', ['status' => 0]);
        $this->assertMaintenanceTableCount(2, false);
        $this->assertMaintenanceTableCount(2);
    }

    /**
     * @covers ::getLatest
     */
    public function testLatest()
    {
        /** @var Maintenance $maintenance */
        $maintenance = $this->app->make(Maintenance::class);

        // Default with nothing in the database should return a default latest stub.
        $latest = $maintenance->getLatest();

        $this->assertDatabaseMissing('maintenance', []);

        $this->assertSame(false, $latest->status);
        $this->assertSame(60, $latest->retry_after);
        $this->assertSame('', $latest->message);

        $maintenance->down();

        $latest = $maintenance->getLatest();

        $this->assertEquals(1, $latest->status);
        $this->assertEquals(60, $latest->retry_after);
        $this->assertSame('', $latest->message);

        $this->assertDatabaseHas('maintenance', ['status' => 1]);
        $this->assertMaintenanceTableCount(1, true);
        $this->assertMaintenanceTableCount(1);

        $maintenance->up();

        $latest = $maintenance->getLatest();

        $this->assertEquals(0, $latest->status);
        $this->assertEquals(60, $latest->retry_after);
        $this->assertSame('', $latest->message);

        $this->assertDatabaseHas('maintenance', ['status' => 0]);
        $this->assertMaintenanceTableCount(1, false);
        $this->assertMaintenanceTableCount(1);
    }

    /**
     * @param int $expected_count
     */
    private function assertMaintenanceTableCount(int $expected_count, $status = null)
    {
        $query = DB::table('maintenance');

        if (isset($status)) {
            $query->where('status', (int) $status);
        }

        $count = $query->count();

        $this->assertEquals($expected_count, $count);
    }
}
