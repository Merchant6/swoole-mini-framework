<?php

namespace App\Database;
use App\Config\DbConfig;
use DI\NotFoundException;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Exception;

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
        ];

        $this->connection = DriverManager::getConnection($connectionParams);
    }

    public function __call(string $name, array $args)
    {
        return call_user_func_array([$this->connection, $name], $args);
    }


    public static function builder(): QueryBuilder
    {
        $db = new static();
        return $db->connection->createQueryBuilder();
    }

    public static function all(string $table, string|array $params): array
    {
        return self::builder()
        ->select($params)
        ->from($table)
        ->fetchAllAssociative();
    }

    public static function find(string $table, int|string $id, string|array $params = '*'): array
    {
        return self::builder()
        ->select(($params === null) ? '*' : $params)
        ->from($table)
        ->where("id = :id")
        ->setParameter('id', $id)
        ->execute()
        ->fetchAllAssociative();
    }

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