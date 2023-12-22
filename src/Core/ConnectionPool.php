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
    
    public function __construct()
    {   
        $this->size = $_ENV['POOL_SIZE'];
        $this->pool = new Channel($this->size);
        for ($i = 0; $i < $this->size; $i++) {
            $entityManager = $this->createEntityManager();
            $this->put($entityManager);
        }
    }

    public function get(): EntityManager
    {   
        return $this->pool->pop();
    }

    public function put(EntityManager $entityManager)
    {
        $this->pool->push($entityManager);
    }

    public function close(): void
    {
        $this->pool->close();
        $this->pool = null;
    }

    protected function createEntityManager(): EntityManager
    {
        $credentials = require(__DIR__ . '/../Config/database.php');
        return EntityManager::create($credentials['db'], Setup::createAttributeMetadataConfiguration([__DIR__ . '/../Entity']));
    }

    public function run(Closure $func)
    {   
        $result = null;

        Coroutine\go(function() use($func, &$result){
            $conn = $this->get();
            $result = $func($conn);
            $this->put($conn);
        });
        
        return $result;
    }

    
}
