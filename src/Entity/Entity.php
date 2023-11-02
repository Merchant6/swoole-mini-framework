<?php

namespace App\Entity;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Setup;

class Entity
{
    /**
     * @var EntityManager
     */
    protected EntityManager $entityManager;

    public function __construct()
    {
        $config = require(__DIR__ . '/../Config/database.php');

        $pool = new \Swoole\ConnectionPool(
            function() use($config){
                return DriverManager::getConnection($config['db']);
            }, 100000
        );

        $this->entityManager =  EntityManager::create($pool->get(), Setup::createAttributeMetadataConfiguration([__DIR__ . '/../Entity']));
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
}
