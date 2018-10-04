<?php

namespace FriendsOfCat\LaravelDbMaintenance;

use Carbon\Carbon;
use Illuminate\Database\ConnectionResolverInterface;

class Maintenance
{

    /**
     * @var string
     */
    protected $connection;

    /**
     * @var \Illuminate\Database\ConnectionResolverInterface
     */
    protected $manager;

    /**
     * Maintenance constructor.
     *
     * @param \Illuminate\Database\ConnectionResolverInterface $manager
     * @param string $connection
     */
    public function __construct(ConnectionResolverInterface $manager, string $connection)
    {
        $this->manager = $manager;
        $this->connection = $connection;
    }

    public function up()
    {
        return $this->getTableBuilder()->insert([
            'created_at' => Carbon::now(),
        ]);
    }

    public function isUp()
    {
        return $this->getTableBuilder()->count();
    }

    public function down()
    {
        return $this->getTableBuilder()->delete();
    }

    public function isDown()
    {
        return !$this->isUp();
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getTableBuilder()
    {
        return $this->getConnection()->table('maintenance');
    }

    /**
     * Resolve the database connection instance.
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    protected function getConnection()
    {
        return $this->manager->connection($this->connection);
    }
}
