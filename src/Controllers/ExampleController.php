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
        // $test = DB::all('test', ['id', 'name', 'email']);
        // $test = DB::find('test', 0);
        $test = DB::findOrFail('test', 10);

        return Response::send(['data' => $test]);
    }

    public function doSomething()
    {
        return "Hello from something";
    }
}