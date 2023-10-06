<?php

namespace App\Controllers;
use App\Core\Response;
use App\Database\DB;

class ExampleController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function index(): mixed
    {
        $test = DB::builder()
        ->select('id', 'name', 'email')
        ->from('test')
        ->fetchAllAssociative();

        return Response::send($test);
    }

    public function doSomething()
    {
        return "Hello from something";
    }
}