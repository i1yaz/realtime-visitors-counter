<?php

require 'vendor/autoload.php';

use Ilyas\Driver\RedisDB;
use Ilyas\DBFactory;
use Ilyas\VisitorsCounter;

$dbFactory = new DBFactory();
$dbFactory->setDriver(RedisDB::class);
$dataBase   = $dbFactory->makeDB(['127.0.0.1','','','']);
$repository = $dbFactory->getRepository($dataBase);
echo VisitorsCounter::getCount($repository);