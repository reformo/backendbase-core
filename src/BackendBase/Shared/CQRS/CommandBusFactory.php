<?php

declare(strict_types=1);

namespace BackendBase\Shared\CQRS;

use BackendBase\Shared\CQRS\Interfaces\CommandBus as CommandBusInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

class CommandBusFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): CommandBusInterface
    {
        $middlewareHandlers  = [new HandleMessageMiddleware(new ContainerAwareHandlersLocator($container))];
        $messengerCommandBus = new MessageBus(
            $middlewareHandlers
        );

        return new CommandBus($messengerCommandBus);
    }
}
