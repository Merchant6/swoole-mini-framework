<?php

namespace App\Core;

class Response
{
    public static int $status;

    public function __construct()
    {

    }

    public static function json(mixed $content = '', int $status = 200): mixed
    {   
        self::$status = $status;
        return json_encode($content, JSON_PRETTY_PRINT);
    }
}
