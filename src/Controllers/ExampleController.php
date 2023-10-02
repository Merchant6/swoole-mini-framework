<?php

namespace App\Controllers;

class ExampleController extends BaseController
{
    public static function __callStatic($method, $args)
    {
        parent::__callStatic($method, $args);
    }

    public function index()
    {
        return "Hello from ExampleController";
    }
}