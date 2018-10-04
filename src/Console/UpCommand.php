<?php

namespace FriendsOfCat\LaravelDbMaintenance\Console;

class UpCommand extends MaintenanceCommandBase
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bring the application out of maintenance mode (DB Maintenance)';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->maintenance->up()) {
            $this->info('Application is now live.');
        } else {
            $this->line('Application is already live.');
        }
    }
}
