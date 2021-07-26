<?php

declare(strict_types=1);

namespace BackendBase\Shared\CQRS;

use BackendBase\Domain\Shared\Exception\ExecutionFailed;
use BackendBase\Shared\CQRS\Interfaces\Query;
use BackendBase\Shared\CQRS\Interfaces\QueryBus as QueryBusInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

final class QueryBus implements QueryBusInterface
{
    use HandleTrait {
        handle as handleQuery;
    }

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->messageBus = $queryBus;
    }

    public function handle(Query $message): mixed
    {
        try {
            return $this->handleQuery($message);
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        } catch (Throwable $exception) {
            throw ExecutionFailed::create($exception->getMessage());
        }
    }
}
