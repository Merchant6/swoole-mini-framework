<?php

namespace App\Controllers;

use Swoole\Http\Request;
use Swoole\Http\Response;

class BaseController
{
    public function __construct(protected Request $request, protected Response $response)
    {

    }
}