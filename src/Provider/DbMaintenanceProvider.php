<?php

namespace FriendsOfCat\LaravelDbMaintenance\Provider;

use FriendsOfCat\LaravelDbMaintenance\Console\DownCommand;
use FriendsOfCat\LaravelDbMaintenance\Console\UpCommand;
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
        $this->app->extend('command.up', function ($command, Application $app) {
            return $app->make(UpCommand::class);
        });
        $this->app->extend('command.down', function ($command, Application $app) {
            return $app->make(DownCommand::class);
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

