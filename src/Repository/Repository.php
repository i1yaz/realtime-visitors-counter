<?php

namespace Ilyas\Repository;

/**
 * Interface Repository
 * @package Ilyas\Repository
 */
interface Repository
{
    const TABLE_NAME = 'online_visitors';

    /**
     * @param $sessionId
     * @return mixed
     */
    public function getVisitorBySessionID($sessionId);

    /**
     * @param $sessionId
     * @param $time
     * @return mixed
     */
    public function addNewVisitor($sessionId, $time);

    /**
     * @param $sessionId
     * @param $time
     * @return mixed
     */
    public function updateVisitor($sessionId, $time);

    /**
     * @param $time
     * @param null $ttl
     * @return mixed
     */
    public function deleteOfflineVisitors($time,$ttl);

    /**
     * @return mixed
     */
    public function getAllVisitors();

    public function close();
}