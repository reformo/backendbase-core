<?php

declare(strict_types=1);

namespace BackendBase\Domain\IdentityAndAccess\Model;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

class User
{
    private UuidInterface $id;
    private Person $person;
    private ContactInformation $contactInformation;
    private bool $isActive;
    private DateTimeImmutable $createdAt;

    public function __construct(UuidInterface $id, Person $person, ContactInformation $contactInformation, bool $isActive, DateTimeImmutable $createdAt)
    {
        $this->id                 = $id;
        $this->person             = $person;
        $this->contactInformation = $contactInformation;
        $this->isActive           = $isActive;
        $this->createdAt          = $createdAt;
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
