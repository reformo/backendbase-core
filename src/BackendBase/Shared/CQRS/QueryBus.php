<?php

declare(strict_types=1);

namespace BackendBase\Shared\CQRS;

use BackendBase\Shared\CQRS\Interfaces\Query;
use BackendBase\Shared\CQRS\Interfaces\QueryBus as QueryBusInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class QueryBus implements QueryBusInterface
{
    use HandleTrait {
        handle as handleQuery;
    }

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->messageBus = $queryBus;
    }

    /** @return mixed */
    public function handle(Query $message)
    {
        return $this->handleQuery($message);
    }
}
