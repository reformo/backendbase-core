<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Query;

use BackendBase\Shared\CQRS\Interfaces\Query;

#[QueryHandler(GetAllUsersPaginatedHandler::class)]
class GetAllUsersPaginated implements Query
{
    public const QUERY_NAME = 'users.get_all_users_paginated';

    public function __construct(
        private string $queryString,
        private int $page,
        private int $pageSize
    ) {
    }

    public function queryString(): string
    {
        return $this->queryString;
    }

    public function page(): int
    {
        return $this->page;
    }

    public function pageSize(): int
    {
        return $this->pageSize;
    }

    public function getQueryName(): string
    {
        return self::QUERY_NAME;
    }
}
