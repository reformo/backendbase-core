<?php

declare(strict_types=1);

namespace BackendBase\Shared\Persistence\Doctrine;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;

use function class_implements;
use function in_array;
use function str_replace;

class DoctrineRepositoryFactory extends ReflectionBasedAbstractFactory
{
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        return in_array(Repository::class, class_implements($requestedName), true);
    }

    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $requestedName = str_replace('Interfaces', 'Persistence\\Doctrine', $requestedName);

        return parent::__invoke($container, $requestedName, $options); // TODO: Change the autogenerated stub
    }
}