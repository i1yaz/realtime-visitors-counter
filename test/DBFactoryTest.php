<?php

namespace Ilyas;

use Ilyas\Driver\RedisDB;
use PDO;
use Ilyas\Driver\MySQLDB;
use Predis\Client;

/**
 * Class OnlineVisitorsTest
 * @package Ilyas
 */
class OnlineVisitorsTest extends PHPUnit_Framework_TestCase
{
    public function testRedisDriver()
    {
        $dbFactory = new DBFactory();
        $dbFactory->setDriver(RedisDB::class);
        $dataBase   = $dbFactory->makeDB(['127.0.0.1','','','']);
        $repository = $dbFactory->getRepository($dataBase);
        $this->assertTrue($repository instanceof Client);
    }

    public function testMySQLDriver()
    {
        $dbFactory = new DBFactory();
        $dbFactory->setDriver(MySQLDB::class);
        $dataBase   = $dbFactory->makeDB(['127.0.0.1','homestead','homestead','secret']);
        $repository = $dbFactory->getRepository($dataBase);
        $this->assertTrue($repository instanceof PDO);
    }

    public function testVisitorsCount()
    {
        $dbFactory = new DBFactory();
        $dbFactory->setDriver(MySQLDB::class);
        $dataBase   = $dbFactory->makeDB(['127.0.0.1','homestead','homestead','secret']);
        $repository = $dbFactory->getRepository($dataBase);
        $this->assertTrue(VisitorsCounter::getCount($repository) > 0);
    }
}