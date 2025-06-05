<?php

use Ilyas\Repository\Repository;

class LaravelSessionCounter 
{
    private $redis;
    private $sessionPrefix;
    
    public function __construct($redisHost = '127.0.0.1', $redisPort = 6379, $sessionPrefix = 'laravel_session:')
    {
        $this->redis = new Redis();
        $this->redis->connect($redisHost, $redisPort);
        $this->sessionPrefix = $sessionPrefix;
    }
    
    public static function getCount(Repository $repository, $second = 300) 
    {
        // Get Laravel session ID from cookie
        $sessionId = self::getLaravelSessionId();
        
        if (!$sessionId) {
            return 0; // No valid Laravel session found
        }
        
        $time = time();
        $timeLimit = $time - $second;
        
        // Check if this session is already tracked
        $num = $repository->getVisitorBySessionID($sessionId);
        
        if (!$num) {
            $repository->addNewVisitor($sessionId, $time);
        } else {
            $repository->updateVisitor($sessionId, $time);
        }
        
        // Get count of active visitors
        $count = $repository->getAllVisitors();
        
        // Clean up old visitors
        $repository->deleteOfflineVisitors($timeLimit);
        
        $repository->close();
        
        return empty($count) ? 0 : count($count);
    }
    
    /**
     * Alternative method that works directly with Redis without Repository
     */
    public function getCountFromRedis($second = 300)
    {
        $sessionId = self::getLaravelSessionId();
        
        if (!$sessionId) {
            return 0;
        }
        
        $time = time();
        $timeLimit = $time - $second;
        
        // Store visitor data in Redis with expiration
        $visitorKey = "online_visitors:{$sessionId}";
        $this->redis->setex($visitorKey, $second, $time);
        
        // Get all online visitors
        $pattern = "online_visitors:*";
        $keys = $this->redis->keys($pattern);
        
        // Clean up expired entries (optional, as Redis will auto-expire)
        $activeCount = 0;
        foreach ($keys as $key) {
            $lastSeen = $this->redis->get($key);
            if ($lastSeen && ($lastSeen > $timeLimit)) {
                $activeCount++;
            } else {
                $this->redis->del($key); // Clean up manually
            }
        }
        
        return $activeCount;
    }
    
    /**
     * Get Laravel session ID from cookie
     */
    private static function getLaravelSessionId()
    {
        // Laravel default session cookie name (you might need to adjust this)
        $sessionName = 'laravel_session';
        
        // Try to get from environment or config if available
        if (file_exists(__DIR__ . '/../.env')) {
            $envContent = file_get_contents(__DIR__ . '/../.env');
            if (preg_match('/SESSION_COOKIE=(.+)/', $envContent, $matches)) {
                $sessionName = trim($matches[1]);
            }
        }
        
        if (!isset($_COOKIE[$sessionName])) {
            return null;
        }
        
        return $_COOKIE[$sessionName];
    }
    
    /**
     * Validate if Laravel session exists in Redis
     */
    public function validateLaravelSession($sessionId)
    {
        $sessionKey = $this->sessionPrefix . $sessionId;
        return $this->redis->exists($sessionKey);
    }
    
    /**
     * Get Laravel session data (optional, for debugging)
     */
    public function getLaravelSessionData($sessionId)
    {
        $sessionKey = $this->sessionPrefix . $sessionId;
        $sessionData = $this->redis->get($sessionKey);
        
        if ($sessionData) {
            // Laravel sessions are serialized, you might need to unserialize
            return unserialize($sessionData);
        }
        
        return null;
    }
    
    public function __destruct()
    {
        if ($this->redis) {
            $this->redis->close();
        }
    }
}

// Usage example with your existing Repository pattern:
/*
$counter = new LaravelSessionCounter();
$count = LaravelSessionCounter::getCount($repository, 300);
echo "Online visitors: " . $count;
*/

// Or use the Redis-only approach:
/*
$counter = new LaravelSessionCounter();
$count = $counter->getCountFromRedis(300);
echo "Online visitors: " . $count;
*/