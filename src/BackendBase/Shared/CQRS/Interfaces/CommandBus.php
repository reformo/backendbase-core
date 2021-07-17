<?php

declare(strict_types=1);

namespace BackendBase\Shared\CQRS\Interfaces;

interface CommandBus
{
    public function dispatch(Command $message): void;
}
