<?php

declare(strict_types=1);

namespace BackendBase\Shared\Persistence\Doctrine;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;

use function class_implements;
use function in_array;

class LazyCollectionFactory extends ReflectionBasedAbstractFactory
{
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        return in_array(LazyCollection::class, class_implements($requestedName), true);
    }
}
