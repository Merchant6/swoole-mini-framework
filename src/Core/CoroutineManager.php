<?php

namespace App\Core;
use Swoole\Coroutine as Co;
use Swoole\Coroutine\Channel;

class CoroutineManager
{
    /**
     * Creare a Coroutine
     * @param int $capacity
     * @param callable $callable
     * @return array
     */
    public static function run(int $capacity = 1, callable $callable): array
    {   
        $channel = new Channel($capacity);
        Co::create(function () use ($callable, $channel) 
        {
            $output = $callable();
            $channel->push($output);
        });

        return $channel->pop();
    }

    /**
     * Return stats of current Coroutine context
     * @return mixed
     */
    public static function stats()
    {
        return Co::stats();
    }

    /**
     * Get the id of the current Coroutine
     * @return mixed
     */
    public static function getId()
    {
        return Co::getCid();
    }
    
}
