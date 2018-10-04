<?php

namespace FriendsOfCat\LaravelDbMaintenance\Console;

class DownCommand extends MaintenanceCommandBase
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'down {--message= : The message for the maintenance mode. }
                                 {--retry=60 : The number of seconds after which the request may be retried.}';

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
        if ($this->maintenance->down((string) $this->option('message'), (int) $this->option('retry'))) {
            $this->comment('Application is now in maintenance mode.');
        } else {
            $this->line('Application is already in maintenance mode.');
        }
    }
}
