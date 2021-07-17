<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Query;

use BackendBase\Shared\CQRS\Interfaces\Query;

class AuthenticateUserWithEmail implements Query
{
    public const QUERY_NAME = 'users.authenticate';
    private string $email;
    private string $password;

    public function __construct(string $email, string $password)
    {
        $this->email    = $email;
        $this->password = $password;
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
