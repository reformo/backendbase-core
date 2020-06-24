<?php

declare(strict_types=1);

namespace BackendBase\Shared\Services\MessageBus;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\Mapping\MapByNamingConvention;
use League\Tactician\Handler\Mapping\MethodName\Invoke;

class TacticianCommandBusFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null) : CommandBus
    {
        $handlerMiddleware = new CommandHandlerMiddleware(
            $container,
            new MapByNamingConvention(
                new CommandClassNameInflector(),
                new Invoke()
            )
        );

        return new CommandBus($handlerMiddleware);
    }
}
