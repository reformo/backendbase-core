<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Query;

use BackendBase\Shared\CQRS\Interfaces\Query;

#[HandlerAttribute(GetUserByIdHandler::class)]
class GetUserById implements Query
{
    public const QUERY_NAME = 'users.get_user_by_id';

    public function __construct(private string $userId)
    {
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function getQueryName(): string
    {
        return self::QUERY_NAME;
    }
}
