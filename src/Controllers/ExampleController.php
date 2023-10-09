<?php

namespace App\Controllers;
use App\Core\Response;
use App\Database\DB;

class ExampleController extends BaseController
{
    public function __construct()
    {
        
    }

    public function index(): mixed
    {
        $test = DB::find('swoole', 1000);

        return Response::send(['data' => $test], 200);
    }

    public function doSomething()
    {
        return "Hello from something";
    }
}