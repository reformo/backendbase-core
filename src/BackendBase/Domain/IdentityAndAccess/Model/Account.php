<?php

declare(strict_types=1);

namespace BackendBase\Domain\IdentityAndAccess\Model;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

class Account
{
    private UuidInterface $id;
    private Person $person;
    private array $permissions;
    private bool $isActive;
    private DateTimeImmutable $createdAt;
}
