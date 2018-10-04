<?php

namespace FriendsOfCat\LaravelDbMaintenance;

use Carbon\Carbon;
use Illuminate\Database\ConnectionResolverInterface;

class Maintenance
{

    /**
     * @var string
     */
    protected $tableName = 'maintenance';

    /**
     * @var string
     */
    protected $connection;

    /**
     * @var \Illuminate\Database\ConnectionResolverInterface
     */
    protected $manager;

    /**
     * @var \stdClass|null
     */
    protected $latest;

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

        $this->reset();

        return (bool) $this->getTableBuilder()
            ->where('id', function ($query) {
                $query->selectRaw('MAX(id)')->from($this->tableName);
            })
            ->update([
                'status' => false,
                'updated_at' => Carbon::now(),
            ]);
    }

    public function isUp()
    {
        return !$this->getLatest()->status;
    }

    public function down()
    {
        if ($this->isDown()) {
            return false;
        }

        $this->reset();

        $now = Carbon::now();

        return $this->getTableBuilder()->insert([
            'created_at' => $now,
            'updated_at' => $now,
            'status' => true,
        ]);
    }

    public function isDown()
    {
        return (bool) $this->getLatest()->status;
    }

    /**
     * @return \stdClass
     */
    public function getLatest()
    {
        if (!isset($this->latest)) {
            $this->latest = $this->getTableBuilder()
                ->orderBy('id', 'desc')
                ->limit(1)
                ->first();

            if (is_null($this->latest)) {
                $this->latest = $this->defaultLatest();
            }
        }

        return $this->latest;
    }

    protected function reset()
    {
        $this->latest = null;
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getTableBuilder()
    {
        return $this->getConnection()->table($this->tableName);
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

    protected function defaultLatest()
    {
        $now = Carbon::now();

        $latest = new \stdClass();
        $latest->status = false;
        $latest->created_at = $now;
        $latest->updated_at = $now;
        $latest->retry_after = 60;
        $latest->message = '';

        return $latest;
    }
}
