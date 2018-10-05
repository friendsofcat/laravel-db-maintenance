<?php

namespace FriendsOfCat\Tests\LaravelDbMaintenance;

use FriendsOfCat\LaravelDbMaintenance\Maintenance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

/**
 * @coversDefaultClass \FriendsOfCat\LaravelDbMaintenance\Provider\DbMaintenanceProvider
 */
class ProviderIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @covers ::boot
     * @covers ::bootGlobalMiddleware
     */
    public function testGlobalMiddleware()
    {
        /** @var Maintenance $maintenance */
        $maintenance = $this->app->make(Maintenance::class);

        $router = $this->app->get('router');
        $router->get('/', function () {
            return new Response();
        });

        $this->get('/')->assertSuccessful();

        $maintenance->down('Site is down!', 99);

        $this->get('/')
            ->assertStatus(503)
            ->assertHeader('Retry-After', 99);

        $maintenance->up();

        $this->get('/')->assertSuccessful();

        $maintenance->down('Site is down again!', 49);

        $this->get('/')
            ->assertStatus(503)
            ->assertHeader('Retry-After', 49);

        $maintenance->up();

        $this->get('/')->assertSuccessful();
    }
}
