<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Command;

#[HandlerAttribute(UnregisterUserHandler::class)]
class UnregisterUser
{
    public function __construct(private string $id)
    {
    }

    public function id()
    {
        return $this->id;
    }
}
