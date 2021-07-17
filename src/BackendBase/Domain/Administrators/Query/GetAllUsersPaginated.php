<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Query;

use BackendBase\Shared\CQRS\Interfaces\Query;

class GetAllUsersPaginated implements Query
{
    public const QUERY_NAME = 'users.get_all_users_paginated';

    private int $offset;
    private int $limit;

    public function __construct(int $offset, int $limit)
    {
        $this->offset = $offset;
        $this->limit  = $limit;
    }

    public function offset(): int
    {
        return $this->offset;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function getQueryName(): string
    {
        return self::QUERY_NAME;
    }
}
