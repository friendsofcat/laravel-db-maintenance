# Laravel Database Maintenance

[![Actions Status](https://github.com/friendsofcat/laravel-db-maintenance/workflows/CI/badge.svg)](https://github.com/friendsofcat/laravel-db-maintenance/actions)

A Database driven replacement for Laravel's default file based maintenance mode. This allows easy maintenance mode across multiple server environments.

### Installation

`composer require friendsofcat/laravel-db-maintenance`

The service provider will be auto discovered. This will replace the default `up` and `down` commands
as well as register a new `CheckDbMaintenance` middleware __globally__. So no need to manually add this to
Kernel $middleware. However, the default `\Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class`
can be removed (optional).
