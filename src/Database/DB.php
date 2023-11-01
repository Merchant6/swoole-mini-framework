<?php

namespace App\Database;
use App\Config\DbConfig;
use DI\NotFoundException;
use Doctrine\DBAL\Query\QueryBuilder;
use Exception;
use Doctrine\DBAL\DriverManager;
use Swoole\ConnectonPool;
class DB
{
    /**
     * @var DbConfig
     */
    protected DbConfig $dbConfig;

    /**
     * @var 
     */
    protected $connection;
    
    /**
     * Getting and Setting the database configuration
     */
    public function __construct()
    {
        $config = require(__DIR__ . '/../Config/database.php');
        
        $this->connection = DriverManager::getConnection($config['db']);
    }

    /**
     * Return Instance of \Doctrine\DBAL\Query\QueryBuilder
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public static function builder(): QueryBuilder
    {
        return (new static())
        ->connection
        ->createQueryBuilder();
    }

    /**
     * Return all records from a given table
     * @param string $table
     * @param string|array $params
     * @return array
     */
    public static function all(string $table, string|array $params = '*'): array
    {
        return self::builder()
        ->select($params)
        ->from($table)
        ->fetchAllAssociative();
    }

    /**
     * Find a record using id
     * @param string $table
     * @param int|string $id
     * @param string|array $params
     * @return array
     */
    public static function find(string $table, int|string $id, string|array $params = '*'): array
    {
        return self::builder()
        ->select($params)
        ->from($table)
        ->where("id = :id")
        ->setParameter('id', $id)
        ->execute()
        ->fetchAllAssociative();
    }

    /**
     * Find record from id but throws an errors when record does not exist, unlike find()
     * @param string $table
     * @param int|string $id
     * @param string|array $params
     * @return array|string
     */
    public static function findOrFail(string $table, int|string $id, string|array $params = '*'): array|string
    {
        $result =  self::builder()
        ->select(($params === null) ? '*' : $params)
        ->from($table)
        ->where("id = :id")
        ->setParameter('id', $id)
        ->execute()
        ->fetchAllAssociative();

        if(!$result)
        {
            return sprintf("Record with the id: '%u' does not exist.", $id);
        }
        return $result;

    }

}