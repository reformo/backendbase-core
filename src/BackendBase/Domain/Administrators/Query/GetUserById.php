<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Query;

use BackendBase\Shared\CQRS\Interfaces\Query;

#[QueryHandler(GetUserByIdHandler::class)]
class GetUserById implements Query
{
    public const QUERY_NAME = 'users.get_user_by_id';
    private string $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
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
