<?php

namespace App\Controllers;

use BadMethodCallException;

class BaseController
{
    public static function __callStatic($method, $args)
    {
        if(method_exists(__CLASS__, $method))
        {
            $instance = new static();
            return call_user_func_array([$instance, $method], $args);
        }

        throw new BadMethodCallException("Static method '$method' does not exists.");
    }
}