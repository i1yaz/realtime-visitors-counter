<?php

namespace Ilyas\Driver;

/**
 * Class Driver
 * @package Ilyas\Driver
 */
abstract class Driver
{
    private $host;
    private $db;
    private $user;
    private $password;

    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    public function setDB($db)
    {
        $this->db = $db;
        return $this;
    }

    public function setUserName($user)
    {
        $this->user = $user;
        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getDB()
    {
        return $this->db;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    protected function close()
    {
        //...
    }

    /**
     * @return mixed
     */
    abstract function connect();
}