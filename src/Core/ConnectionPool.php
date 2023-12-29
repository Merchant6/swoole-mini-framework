<?php

namespace App\Core;
use Closure;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

class ConnectionPool
{
    protected Channel $pool;
    protected int $size;
    protected int $minSize;
    protected int|float $maxAquireTime;
    protected int|float $maxIdleTime;
    protected array $lastActiveTime = [];
    
    public function __construct()
    {   
        $this->size = $_ENV['MAX_CONNECTIONS'];
        $this->minSize = $_ENV['MIN_CONNECTIONS'];
        $this->maxAquireTime = $_ENV['MAX_AQUIRE_TIME'];
        $this->maxIdleTime = $_ENV['MAX_IDLE_TIME'];

        $this->pool = new Channel($this->size);
        for ($i = 0; $i < $this->size; $i++) {
            $entityManager = $this->createEntityManager();
            $this->put($entityManager, $this->maxAquireTime);
        }
    }

    public function get(): EntityManager
    {   
        $entityManager = $this->pool->pop();
        
        $this->lastActiveTime[spl_object_hash($entityManager)] = time();

        return $entityManager;
    }

    public function put(EntityManager $entityManager, int $timeout): void
    {
        $this->pool->push($entityManager, $timeout);

        $this->lastActiveTime[spl_object_hash($entityManager)] = time();
    }

    public function close(): void
    {
        $this->pool->close();
    }

    protected function createEntityManager(): EntityManager
    {
        $credentials = require(__DIR__ . '/../Config/database.php');
        return EntityManager::create($credentials['db'], Setup::createAttributeMetadataConfiguration([__DIR__ . '/../Entity']));
    }

    public function run(Closure $func): mixed
    {   
        $result = null;

        if($this->size <= $this->minSize)
        {
            throw new \RuntimeException("MAX pool connections cannot be lesser than or equals to MIN pool connections.");
        }

        Coroutine\go(function() use($func, &$result){

            $conn = $this->get();
            $result = $func($conn);
            $this->put($conn, $this->maxAquireTime);

            if($this->pool->length() > $this->minSize)
            {
                $this->closeIdleConnections();    
            }
            // echo ( json_encode($this->pool->length(), JSON_PRETTY_PRINT)) . PHP_EOL;
        });
        return $result;
    }

    protected function closeIdleConnections()
    {
        $now = time();
        foreach ($this->lastActiveTime as $hash => $lastActiveTime) {
            if ($now - $lastActiveTime > $this->maxIdleTime) {
                $this->removeConnection($hash);
            }
        }
    }

    protected function removeConnection(string $hash)
    {
        $connection = $this->pool->pop();
        if ($connection instanceof EntityManager) {
            $connection->close();
            // echo 'Connection closed: ' . $hash . PHP_EOL;
        }
        unset($this->lastActiveTime[$hash]);
    }
}
