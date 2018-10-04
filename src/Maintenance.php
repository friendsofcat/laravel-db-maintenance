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
        if ($this->isUp()) {
            return false;
        }

        return (bool) $this->getTableBuilder()
            ->where('id', function ($query) {
                $query->selectRaw('MAX(id)')->from('maintenance');
            })
            ->update([
                'status' => false,
                'updated_at' => Carbon::now(),
            ]);
    }

    public function isUp()
    {
        return !$this->isDown();
    }

    public function down()
    {
        if ($this->isDown()) {
            return false;
        }

        $now = Carbon::now();

        return $this->getTableBuilder()->insert([
            'created_at' => $now,
            'updated_at' => $now,
            'status' => true,
        ]);
    }

    public function isDown()
    {
        return $this->getTableBuilder()
            ->where('status', true)
            ->orderBy('id', 'desc')
            ->limit(1)
            ->exists();
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
