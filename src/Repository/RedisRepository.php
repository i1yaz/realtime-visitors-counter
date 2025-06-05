<?php

namespace Ilyas\Repository;

use Predis\Client;
use Predis\Collection\Iterator\HashKey;

/**
 * Class RedisRepository
 * @package Ilyas\Repository
 */
class RedisRepository implements Repository
{
    protected $redis_key;

    /** @var Client */
    private $db = null;

    /**
     * RedisRepository constructor.
     * @param $db
     */
    public function __construct($db,$redis_key = 'VISITORS')
    {
        $this->db  = $db;
        $this->redis_key = $redis_key;
    }

    /**
     * @param $sessionId
     * @return mixed
     */
    public function getVisitorBySessionID($sessionId)
    {
        return $this->db->hget($this->redis_key, $sessionId);
    }

    /**
     * @param $sessionId
     * @param $time
     * @return mixed
     */
    public function addNewVisitor($sessionId, $time)
    {
        return $this->db->hset($this->redis_key, $sessionId, $time);
    }

    /**
     * @param $sessionId
     * @param $time
     * @return mixed
     */
    public function updateVisitor($sessionId, $time)
    {
        return $this->db->hset($this->redis_key, $sessionId, $time);
    }

    /**
     * @param $time
     * @return int
     */
    public function deleteOfflineVisitors($time)
    {
        $cleanupKey = $this->redis_key . '_last_cleanup_time';

        $lastCleanup = $this->db->get($cleanupKey);
        if ($lastCleanup && ($time - $lastCleanup < 30)) {
            return false; 
        }
        $this->db->set($cleanupKey, $time);
        $fields = [];
        $it = new HashKey($this->db, $this->redis_key);
        foreach ($it as $sessionId => $value) {
            if ($value < $time) {
                $fields[] = $sessionId;
            }
        }

        if (!empty($fields)) {
            $this->db->hdel($this->redis_key, $fields);
        }
        return true;
    }


    /**
     * @return array
     */
    public function getAllVisitors()
    {
        return $this->db->hgetall($this->redis_key);
    }

    public function close()
    {
        $this->db->disconnect();
    }
}