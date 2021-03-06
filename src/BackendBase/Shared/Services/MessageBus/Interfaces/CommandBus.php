<?php

declare(strict_types=1);

namespace BackendBase\Shared\Services\MessageBus\Interfaces;

interface CommandBus
{
    /**
     * Executes the given command and optionally returns a value
     *
     * @return mixed
     *
     * @var object
     */
    public function handle(object $command);
}
