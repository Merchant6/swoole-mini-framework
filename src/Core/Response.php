<?php

namespace App\Core;

class Response
{
    public function __construct()
    {

    }

    public static function send(mixed $content = ''): mixed
    {   
        return json_encode($content, JSON_PRETTY_PRINT);
    }
}
