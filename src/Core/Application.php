<?php

namespace App\Core;
use Swoole\Http\Request;
use Swoole\Http\Response;
use App\Database\DB;
use App\Config\DbConfig;

class Application
{
    public Router $router;
    public DB $db;

    public DbConfig $dbConfig;

    public function __construct(public Request $request, public Response $response)
    {
        //Initailizing the Router class
        $this->router = new Router($request, $response);
    }

}
