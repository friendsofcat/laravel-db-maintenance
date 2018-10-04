<?php

namespace FriendsOfCat\LaravelDbMaintenance\Provider;

use FriendsOfCat\LaravelDbMaintenance\Console\SiteDownCommand;
use FriendsOfCat\LaravelDbMaintenance\Console\SiteUpCommand;
use FriendsOfCat\LaravelDbMaintenance\Maintenance;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class DbMaintenanceProvider extends ServiceProvider
{

    /**
     * Register any application authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootMigrations();
        $this->publishesConfiguration();

        $this->app->singleton(Maintenance::class, function ($app) {
            return new Maintenance($app->make('db'), config('db_maintenance.connection', 'mysql'));
        });

        $this->overrideIlluminateMaintenanceCommands();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/db_maintenance.php', 'db_maintenance');
    }

    protected function overrideIlluminateMaintenanceCommands()
    {
        $this->app->bind('command.up', function (Application $app) {
            return $app->make(SiteUpCommand::class);
        });
        $this->app->bind('command.down', function (Application $app) {
            return $app->make(SiteDownCommand::class);
        });
    }

    /**
     * Define the migrations.
     */
    protected function bootMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    /**
     * Publishes configuration files.
     */
    protected function publishesConfiguration()
    {
        $this->publishes([
            __DIR__ . '/../../db_maintenance.php' => config_path('db_maintenance.php'),
        ], 'config');
    }
}

