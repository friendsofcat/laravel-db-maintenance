<?php

namespace FriendsOfCat\LaravelDbMaintenance\Console;

class DownCommand extends MaintenanceCommandBase
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'down';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Put the application into maintenance mode (DB Maintenance)';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->maintenance->down()) {
            $this->comment('Application is now in maintenance mode.');
        } else {
            $this->line('Application is already in maintenance mode.');
        }
    }
}
