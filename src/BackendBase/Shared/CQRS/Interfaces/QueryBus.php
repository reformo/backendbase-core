<?php

declare(strict_types=1);

namespace BackendBase\Shared\CQRS\Interfaces;

interface QueryBus
{
    public function handle(Query $message): mixed;
}
