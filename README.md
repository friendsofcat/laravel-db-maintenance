# Laravel Database Maintenance

A Database driven replacement for Laravel's default file base maintenance mode. This allows easy maintenance mode across multiple servers.

### Installation

`composer require friendsofcat/laravel-db-maintenance`

The service provider will be auto discovered. This will replace the default `up` and `down` commands
as well as register a new `CheckDbMaintenance` middleware __globally__. So no need to manually add this to
Kernel $middleware. However, the default `\Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class`
can be removed (optional).
