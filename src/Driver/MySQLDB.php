<?php

namespace Ilyas\Driver;

use PDO;
use PDOException;

/**
 * Class MySQLDB
 * @package Ilyas\Driver
 */
class MySQLDB extends Driver
{
    private static $_instance; //The single instance

    public static function build(Driver $driver)
    {
        $dsn = 'mysql:dbname=' . $driver->getDB() . ';host=' . $driver->getHost();
        try {
            self::$_instance = new PDO($dsn, $driver->getUserName(), $driver->getPassword());
        } catch (PDOException $e) {
            echo 'Error connect: ' . $e->getMessage();
        }
    }

    /**
     * @param Driver $driver
     * @return mixed
     */
    public static function getInstance(Driver $driver)
    {
        if (!self::$_instance) { // If no instance then make one
            self::build($driver);
        }
        return self::$_instance;
    }

    /**
     * @return mixed
     */
    public function connect()
    {
        return self::getInstance($this);
    }
}