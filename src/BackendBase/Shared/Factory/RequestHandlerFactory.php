<?php

declare(strict_types=1);

namespace BackendBase\Shared\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Psr\Http\Server\RequestHandlerInterface;

use function class_implements;
use function in_array;

class RequestHandlerFactory extends ReflectionBasedAbstractFactory
{
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return in_array(RequestHandlerInterface::class, class_implements($requestedName), true);
    }
}
