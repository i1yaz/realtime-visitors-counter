<?php

namespace Ilyas;

use Ilyas\Driver\Driver;
use PDO;
use Predis\Client;
use Exception;
use Ilyas\Repository\PDORepository;
use Ilyas\Repository\RedisRepository;

/**
 * Class DBFactory
 * @package Ilyas
 */
class DBFactory
{
    protected $driver = null;

    /**
     * @param $driver
     */
    public function setDriver($driver)
    {
        $this->driver = new $driver();
    }

    /**
     * @param array $config
     * @return mixed
     * @throws Exception
     */
    public function makeDB(array $config)
    {
        if (!$this->driver instanceof Driver) {
           throw new Exception('Not fount Driver');
        }

        $db = $this->driver;
        list($host, $dbname, $user, $password) = $config;
        $db->setHost($host)
           ->setDB($dbname)
           ->setUserName($user)
           ->setPassword($password);

        return $db->connect();
    }

    /**
     * @param PDO|mixed $handle
     * @param string $redisKey
     * @return PDORepository|RedisRepository
     */
    public function getRepository($handle,$redisKey=null)
    {
        if ($handle instanceof PDO){
            return new PDORepository($handle);
        }

        if ($handle instanceof Client){
            return new RedisRepository($handle,$redisKey);
        }

        return false;
    }
}
