<?php

namespace FriendsOfCat\Tests\LaravelDbMaintenance;

use FriendsOfCat\LaravelDbMaintenance\Provider\DbMaintenanceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{

    /**
     * Get test package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            DbMaintenanceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $this->setTestConfiguration($app);
    }

    private function setTestConfiguration($app)
    {
        $config = $app['config'];

        $config->set('database.default', 'sqlite');

        $config->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        // Important, set the package config to use te sqlite connection.
        $config->set('db_maintenance.connection', 'sqlite');

        $config->set('app.debug', true);
    }
}
