<?php

namespace App\Core;
use Swoole\Coroutine as co;
use Swoole\Coroutine\Channel;

class CoroutineManager
{
    public static function run(int $capacity = 1, callable $callable) 
    {
        $channel = new Channel($capacity);
        co::create(function () use ($callable, $channel) 
        {
            $output = $callable();
            $channel->push($output);
        });

        return $channel->pop();
    }
}
