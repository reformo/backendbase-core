<?php

declare(strict_types=1);

namespace BackendBase\Shared\ValueObject\Interfaces;

use Ramsey\Uuid\UuidInterface;

interface Identifier
{
    public function id() : UuidInterface;

    public function toString() : string;
}
