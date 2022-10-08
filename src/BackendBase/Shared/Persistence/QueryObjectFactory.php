<?php

declare(strict_types=1);

namespace BackendBase\Shared\Persistence;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;

use function class_implements;
use function in_array;

class QueryObjectFactory extends ReflectionBasedAbstractFactory
{
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        return class_exists($requestedName) && in_array(QueryObject::class, class_implements($requestedName), true);
    }
}
