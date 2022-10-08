<?php

declare(strict_types=1);

namespace BackendBase\Shared\CQRS;

use BackendBase\Shared\CQRS\Interfaces\Query;
use Psr\Container\ContainerInterface;

use function class_implements;
use function in_array;

class QueryHandlerFactory extends CommandHandlerFactory
{
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        return class_exists($requestedName) && in_array(Query::class, class_implements($requestedName), true);
    }
}
