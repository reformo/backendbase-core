<?php

declare(strict_types=1);

namespace BackendBase\Shared\CQRS;

use BackendBase\Domain\Shared\Exception\ExecutionFailed;
use BackendBase\Shared\CQRS\Interfaces\Command;
use BackendBase\Shared\CQRS\Interfaces\CommandBus as CommandBusInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

final class CommandBus implements CommandBusInterface
{
    private MessageBusInterface $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function dispatch(Command $message): void
    {
        try {
            $this->commandBus->dispatch($message);
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        } catch (Throwable $exception) {
            throw ExecutionFailed::create($exception->getMessage());
        }
    }
}
