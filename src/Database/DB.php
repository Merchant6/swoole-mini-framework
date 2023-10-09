<?php

namespace App\Database;
use App\Config\DbConfig;
use DI\NotFoundException;
use Doctrine\DBAL\Query\QueryBuilder;
use Exception;
use Doctrine\DBAL\{Driver, DriverManager};
class DB
{
    protected DbConfig $dbConfig;
    protected $connection;
    
    public function __construct()
    {
        $dbConfig = new DbConfig();
        $config = $dbConfig->config['db'];

        $connectionParams = [
            'dbname' => $config['dbname'],
            'user' => $config['user'],
            'password' => $config['password'],
            'host' => $config['host'],
            'driver' => $config['driver'],
            // 'driverClass' => Driver\Swoole\Coroutine\Mysql\Driver::class,
            'poolSize' => 8,
        ];

        $this->connection = DriverManager::getConnection($connectionParams);
    }

    // public function __call(string $name, array $args)
    // {
    //     return call_user_func_array([$this->connection, $name], $args);
    // }


    /**
     * Return Instance of \Doctrine\DBAL\Query\QueryBuilder
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public static function builder(): QueryBuilder
    {
        $db = new static();
        return $db->connection->createQueryBuilder();
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