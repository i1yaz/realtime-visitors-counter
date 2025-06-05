<?php

namespace Ilyas\Driver;

use Predis\Autoloader;
use Predis\Client;

/**
 * Class RedisDB
 * @package Ilyas\Driver
 */
class RedisDB  extends Driver
{
    public function connect()
    {
        Autoloader::register();
        return new Client([
            'scheme' => 'tcp',
            'host'   => $this->getHost(),
            'port'   => 6379,
        ]);
    }
}