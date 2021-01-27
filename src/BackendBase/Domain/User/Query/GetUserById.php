<?php

declare(strict_types=1);

namespace BackendBase\Domain\User\Query;

class GetUserById
{
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function id(): string
    {
        return $this->id;
    }
}
