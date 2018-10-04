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

    /**
     * @return bool
     */
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
                'updated_at' => $this->nowTimestamp(),
            ]);
    }

    /**
     * @return bool
     */
    public function isUp()
    {
        return !$this->getLatest()->status;
    }

    /**
     * @param string $message
     * @param int $retry_after
     *
     * @return bool
     */
    public function down($message = '', $retry_after = 60)
    {
        if ($this->isDown()) {
            return false;
        }

        $this->reset();

        $now = $this->nowTimestamp();

        return $this->getTableBuilder()->insert([
            'created_at' => $now,
            'updated_at' => $now,
            'status' => true,
            'retry_after' => $retry_after,
            'message' => $message,
        ]);
    }

    /**
     * @return bool
     */
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

    /**
     * Reset latest.
     */
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

    /**
     * @return \stdClass
     */
    protected function defaultLatest()
    {
        $now = $this->nowTimestamp();

        $latest = new \stdClass();
        $latest->status = false;
        $latest->created_at = $now;
        $latest->updated_at = $now;
        $latest->retry_after = 60;
        $latest->message = '';

        return $latest;
    }

    /**
     * @return int
     */
    protected function nowTimestamp()
    {
        return Carbon::now()->timestamp;
    }
}
