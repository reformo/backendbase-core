<?php

declare(strict_types=1);

namespace BackendBase\Shared\CQRS\Interfaces;

interface Command
{
    public function getCommandName(): string;
}
