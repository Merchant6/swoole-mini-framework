<?php

namespace App\Core;
use DI\Container;

class MethodInvoker
{   
    private \ReflectionMethod $reflectMethod;

    public function __construct(protected object $class, protected string $method, protected mixed $routeVars, protected Container $container)
    {
        $this->reflectMethod = new \ReflectionMethod($class, $method);
    }

    public function invoke(): mixed
    {
        $resolvedParams = [];

        foreach($this->routeVars as $name => $value)
        {
            $resolvedParams[$name] = $value;
        }

        $refelectedParams = $this->reflectMethod->getParameters();

        foreach($refelectedParams as $param)
        {
            $paramType = $param->getType();

            if(!$paramType->isBuiltIn() && is_object($param))
            {
                $paramName = $param->getName();

                $className = ucfirst($paramName);
                $class = $this->container->get($className);

                $resolvedParams[$paramName] = $class;
            }

            continue;
        }

        return $this
        ->reflectMethod
        ->invokeArgs($this->class, $resolvedParams);

    }
}
