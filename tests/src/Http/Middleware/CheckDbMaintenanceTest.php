<?php

namespace FriendsOfCat\Tests\LaravelDbMaintenance\Http\Middleware;

use Carbon\Carbon;
use FriendsOfCat\LaravelDbMaintenance\Http\Middleware\CheckDbMaintenance;
use FriendsOfCat\LaravelDbMaintenance\Maintenance;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @coversDefaultClass \FriendsOfCat\LaravelDbMaintenance\Http\Middleware\CheckDbMaintenance
 */
class CheckDbMaintenanceTest extends TestCase
{

    use ProphecyTrait;

    /**
     * @covers ::handle
     */
    public function testHandleWhenUp()
    {
        $maintenance = $this->prophesize(Maintenance::class);
        $maintenance->isDown()
            ->willReturn(false);

        $middleware = new CheckDbMaintenance($maintenance->reveal());

        $next = function () {
            return 'TRUE';
        };

        $request = Request::create('/');

        $this->assertEquals('TRUE', $middleware->handle($request, $next));
    }

    /**
     * @covers ::handle
     */
    public function testHandleWhenDown()
    {
        $this->expectException(MaintenanceModeException::class);

        Carbon::setTestNow();

        $maintenance = $this->prophesize(Maintenance::class);
        $maintenance->isDown()
            ->willReturn(true);

        $latest = new \stdClass();
        $latest->status = true;
        $latest->created_at = Carbon::now()->timestamp;
        $latest->updated_at = Carbon::now()->timestamp;
        $latest->retry_after = 120;
        $latest->message = 'Site is Down';

        $maintenance->getLatest()
            ->willReturn($latest);

        $middleware = new CheckDbMaintenance($maintenance->reveal());

        $next = function () {
        };

        $request = Request::create('/');

        $middleware->handle($request, $next);
    }
}
