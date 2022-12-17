<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Query;

use BackendBase\Shared\CQRS\Interfaces\Query;

#[HandlerAttribute(AuthenticateUserWithEmailHandler::class)]
class AuthenticateUserWithEmail implements Query
{
    public const QUERY_NAME = 'users.authenticate';

    public function __construct(private string $email, private string $password)
    {
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function getQueryName(): string
    {
        return self::QUERY_NAME;
    }
}
