<?php

declare(strict_types=1);

namespace BackendBase\Domain\User\Query;

class GetAllUsers
{
    private int $offset;
    private int $limit;

    public function __construct(int $offset, int $limit)
    {
        $this->offset = $offset;
        $this->limit  = $limit;
    }

    public function offset() : int
    {
        return $this->offset;
    }

    public function limit() : int
    {
        return $this->limit;
    }
}
