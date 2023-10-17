<?php

namespace App\Config;

class DbConfig 
{
    public array $config = [];

    public function __construct()
    {
        $this->config = [

            'db' => [
                'dbname' => $_ENV['DB_DATABASE'],
                'user' => $_ENV['DB_USER'],
                'password' => $_ENV['DB_PASS'],
                'host' => $_ENV['DB_HOST'],
                'driver' => $_ENV['DB_DRIVER'],
            ]

        ];
        
    }
}

// return [

//     'db' => [
//         'dbname' => $_ENV['DB_DATABASE'],
//         'user' => $_ENV['DB_USER'],
//         'password' => $_ENV['DB_PASS'],
//         'host' => $_ENV['DB_HOST'],
//         'driver' => $_ENV['DB_DRIVER'],
//     ]
// ];