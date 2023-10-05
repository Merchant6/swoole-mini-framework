<?php

namespace App\Database;
use App\Config\DbConfig;
use Doctrine\DBAL\DriverManager;

class DB
{
    protected $connection;
    

    public function __construct(DbConfig $dbConfig)
    {
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

}