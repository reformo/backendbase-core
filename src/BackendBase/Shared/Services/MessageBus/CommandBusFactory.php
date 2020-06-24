<?php

declare(strict_types=1);

namespace BackendBase\Shared\Services\MessageBus;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use League\Tactician\CommandBus as TacticianCommandBus;
use BackendBase\Shared\Services\MessageBus\Interfaces\CommandBus as CommandBusInterface;

class CommandBusFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null) : CommandBusInterface
    {
        $commandBus = $container->get(TacticianCommandBus::class);

        return new CommandBus($commandBus);
    }
}
