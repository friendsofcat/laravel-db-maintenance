<?php

namespace FriendsOfCat\LaravelDbMaintenance\Http\Middleware;

use FriendsOfCat\LaravelDbMaintenance\Maintenance;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Illuminate\Http\Request;

class CheckDbMaintenance
{

    /**
     * @var \FriendsOfCat\LaravelDbMaintenance\Maintenance
     */
    protected $maintenance;

    /**
     * CheckDbMaintenance constructor.
     *
     * @param \FriendsOfCat\LaravelDbMaintenance\Maintenance $maintenance
     */
    public function __construct(Maintenance $maintenance)
    {
        $this->maintenance = $maintenance;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, $next)
    {
        if ($this->maintenance->isDown()) {
            $latest = $this->maintenance->getLatest();
            throw new MaintenanceModeException($latest->created_at, $latest->retry_after, $latest->message);
        }

        return $next($request);
    }
}
