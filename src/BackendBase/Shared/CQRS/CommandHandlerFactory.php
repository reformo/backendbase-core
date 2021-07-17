<?php

declare(strict_types=1);

namespace BackendBase\Shared\CQRS;

use BackendBase\Shared\CQRS\Interfaces\CommandHandler;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;

use function class_implements;
use function in_array;

class CommandHandlerFactory extends ReflectionBasedAbstractFactory
{
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        return in_array(CommandHandler::class, class_implements($requestedName), true);
    }

    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return parent::__invoke($container, $requestedName . 'Handler', $options);
    }
}
