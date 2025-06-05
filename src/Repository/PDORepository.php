<?php

namespace Ilyas\Repository;

use PDO;
use PDOException;

/**
 * Class PDORepository
 * @package Ilyas\Repository
 */
class PDORepository implements Repository
{
    /** @var null|PDO */
    private $pdo = null;

    /**
     * PDORepository constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param $sessionId
     * @return bool
     */
    public function getVisitorBySessionID($sessionId)
    {
        $stmt = $this->pdo->prepare('SELECT count(*) FROM ' . self::TABLE_NAME . ' WHERE session_id = :session_id');
        $stmt->bindParam(":session_id", $sessionId);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * @param $sessionId
     * @param $time
     * @return bool
     */
    public function addNewVisitor($sessionId, $time)
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO ". self::TABLE_NAME ." (session_id, time) VALUES (:session_id, :time)");
            $stmt->bindParam(":session_id", $sessionId);
            $stmt->bindParam(":time", $time);
            return $stmt->execute();
        } catch (PDOException $e) {
            var_dump($stmt->errorInfo());
        }
        return false;
    }

    /**
     * @param $sessionId
     * @param $time
     * @return bool
     */
    public function updateVisitor($sessionId, $time)
    {
        $stmt = $this->pdo->prepare('UPDATE ' . self::TABLE_NAME . ' SET time = :time WHERE session_id = :session_id');
        $stmt->bindParam(":time", $time);
        $stmt->bindParam(":session_id", $sessionId);
        return $stmt->execute();
    }

    /**
     * @param $time
     * @return mixed
     */
    public function deleteOfflineVisitors($time,$ttl)
    {
        $stmt = $this->pdo->prepare('DELETE FROM '. self::TABLE_NAME .' WHERE time < :time');
        $stmt->bindParam(":time", $time);
        return $stmt->execute();
    }

    /**
     * @return int
     */
    public function getAllVisitors()
    {
        $stmt = $this->pdo->prepare('SELECT count(*) FROM ' . self::TABLE_NAME);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function close()
    {
        // TODO: Implement close() method.
    }
}