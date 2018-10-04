<?php

namespace FriendsOfCat\LaravelDbMaintenance\Console;

use FriendsOfCat\LaravelDbMaintenance\Maintenance;
use Illuminate\Console\Command;

abstract class MaintenanceCommandBase extends Command
{

    /**
     * @var \FriendsOfCat\LaravelDbMaintenance\Maintenance
     */
    protected $maintenance;

    /**
     * MaintenanceCommandBase constructor.
     *
     * @param \FriendsOfCat\LaravelDbMaintenance\Maintenance $maintenance
     */
    public function __construct(Maintenance $maintenance) {
        parent::__construct();

        $this->maintenance = $maintenance;
    }
}
