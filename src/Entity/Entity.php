<?php

namespace App\Entity;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Setup;
use Swoole\Coroutine as Co;
use Swoole\Database\PDOConfig;
use Swoole\Database\PDOPool;


class Entity
{
    /**
     * @var EntityManager
     */

    /**
     * @var PDOPool
     */
    protected PDOPool $pdoPool;

    protected EntityManager $entityManager;

    public function __construct()
    {
        $credentials = require(__DIR__ . '/../Config/database.php');

        $pool = new \Swoole\ConnectionPool(
            function() use ($credentials) {
                return DriverManager::getConnection($credentials['db']);
            },
            100
        );
        
        $configuration = Setup::createAttributeMetadataConfiguration([__DIR__ . '/../Entity']);
        
        Co::create(function() use ($pool, $configuration) {
            $connection = $pool->get();
            $this->entityManager = EntityManager::create($connection, $configuration);
            var_dump($pool->get());
            $pool->close($connection);
        });
    }


    /**
     * Return an instance of Entity Manager
     * @return EntityManager
     */
    public static function getInstance(): EntityManager
    {
        return (new static)->entityManager;
    }

    /**
     * Return instance of \Doctrine\ORM\QueryBuilder
     * @return \Doctrine\ORM\QueryBuilder
     */
    public static function builder(): QueryBuilder
    {
        return (new static)->entityManager->createQueryBuilder();
    }

    public static function persistAndFlush(object $entity): void
    {
        $entityManager = (new static)->entityManager;
        $entityManager->persist($entity);
        $entityManager->flush();
    }

    public function pool()
    {
        Co::create(function()
        {
            $pool = new PDOPool((new PDOConfig)
            ->withHost('127.0.0.1')
            ->withPort(3306)
            ->withDbName('swoole-mini')
            ->withCharset('utf8mb4')
            ->withUsername('merchant')
            ->withPassword('Thealien862!')
            );

            for ($n = 1024; $n--;) {
                var_dump(json_encode($pool->get(), JSON_PRETTY_PRINT));
            }
        });
    }
}
