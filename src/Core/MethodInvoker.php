<?php

namespace App\Core;
use DI\Container;

class MethodInvoker
{   
    private \ReflectionMethod $reflectMethod;

    private array $refelectedParams;

    public function __construct(protected object $class, protected string $method, protected mixed $routeVars, protected Container $container)
    {
        $this->reflectMethod = new \ReflectionMethod($class, $method);
        $this->refelectedParams = $this->reflectMethod->getParameters();
    }

    /**
     * This method merges the routes params and method 
     * params and invoke them using ReflectionMethod class
     * 
     * @return mixed
     */
    public function invoke(): mixed
    {   
        $resolvedParams = [];

        foreach($this->routeVars as $name => $value)
        {
            $resolvedParams[$name] = $value;
        }

        $refelectedParams = $this->refelectedParams;

        foreach($refelectedParams as $param)
        {
            $paramType = $param->getType();

            if(!$paramType->isBuiltIn() && is_object($param))
            {
                $paramName = $param->getName();

                $className = $param->getType()->getName();
                $classInstance = $this->container->get($className);

                $resolvedParams[$paramName] = $classInstance;
            }

            continue;
        }
        
        return $this
        ->reflectMethod
        ->invokeArgs($this->class, $resolvedParams);
    }
}
