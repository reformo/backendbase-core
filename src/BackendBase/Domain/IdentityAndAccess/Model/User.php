<?php

declare(strict_types=1);

namespace BackendBase\Domain\IdentityAndAccess\Model;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

class User
{
    public function __construct(private UuidInterface $id, private Person $person, private ContactInformation $contactInformation, private bool $isActive, private DateTimeImmutable $createdAt)
    {
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getPerson(): Person
    {
        return $this->person;
    }

    public function getContactInformation(): ContactInformation
    {
        return $this->contactInformation;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
