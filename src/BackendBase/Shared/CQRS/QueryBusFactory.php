<?php

declare(strict_types=1);

namespace BackendBase\Shared\CQRS;

use BackendBase\Shared\CQRS\Interfaces\QueryBus as QueryBusInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

class QueryBusFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): QueryBusInterface
    {
        $middlewareHandlers = [new HandleMessageMiddleware(new ContainerAwareHandlersLocator($container))];
        $messengerQueryBus  = new MessageBus(
            $middlewareHandlers
        );

        return new QueryBus($messengerQueryBus);
    }
}
