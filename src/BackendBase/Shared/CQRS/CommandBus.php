<?php

declare(strict_types=1);

namespace BackendBase\Shared\CQRS;

use BackendBase\Shared\CQRS\Interfaces\Command;
use BackendBase\Shared\CQRS\Interfaces\CommandBus as CommandBusInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CommandBus implements CommandBusInterface
{
    private MessageBusInterface $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function dispatch(Command $message): void
    {
        $this->commandBus->dispatch($message);
    }
}
