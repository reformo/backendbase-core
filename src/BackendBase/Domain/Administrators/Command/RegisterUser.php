<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Command;

use BackendBase\Shared\CQRS\Interfaces\Command as CommandInterface;

#[QueryHandler(RegisterUserHandler::class)]
class RegisterUser implements CommandInterface
{
    public const COMMAND_NAME = 'user.register';

    private array $payload;
    private string $id;

    public function __construct(string $id, array $payload)
    {
        $this->id      = $id;
        $this->payload = $payload;
    }

    public function id()
    {
        return $this->id;
    }

    public function firstName(): string
    {
        return $this->payload['firstName'];
    }

    public function lastName(): string
    {
        return $this->payload['lastName'];
    }

    public function email(): string
    {
        return $this->payload['email'];
    }

    public function passwordHash(): string
    {
        return $this->payload['passwordHash'];
    }

    public function role(): string
    {
        return $this->payload['role'];
    }

    public function getCommandName(): string
    {
        return self::COMMAND_NAME;
    }
}
