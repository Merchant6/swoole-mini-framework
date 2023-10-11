<?php

namespace App\Entity;
use App\Config\DbConfig;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Setup;

class Entity
{
    protected EntityManager $entityManager;

    public function __construct()
    {
        $dbConfig = new DbConfig();
        $config = $dbConfig->config['db'];

        $this->entityManager =  EntityManager::create($config, Setup::createAttributeMetadataConfiguration([__DIR__ . '/../Entity']));
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

    public static function persistAndFlush(object $entity)
    {
        $entityManager = (new static)->entityManager;
        $entityManager->persist($entity);
        $entityManager->flush();
    }
}
