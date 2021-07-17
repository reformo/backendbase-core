<?php

declare(strict_types=1);

namespace BackendBase\Shared\CQRS;

use BackendBase\Shared\CQRS\Interfaces\QueryHandler;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;

use function class_exists;
use function class_implements;
use function in_array;

class QueryHandlerFactory extends ReflectionBasedAbstractFactory
{
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        $className = $requestedName . 'Handler';

        return class_exists($className) && in_array(QueryHandler::class, class_implements($className), true);
    }

    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return parent::__invoke($container, $requestedName . 'Handler', $options);
    }
}
