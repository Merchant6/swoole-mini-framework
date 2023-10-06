<?php

namespace App\Database;
use App\Config\DbConfig;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;

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


    public static function builder()
    {
        $db = new static();
        return $db->connection->createQueryBuilder();
    }
}