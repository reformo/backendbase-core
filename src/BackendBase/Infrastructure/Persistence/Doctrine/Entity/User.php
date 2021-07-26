<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users', schema: 'admin')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private string $id;

    #[ORM\Column(type: 'string')]
    private string $email;

    #[ORM\Column(name: 'password_hash', type: 'string')]
    private string $passwordHash;

    #[ORM\Column(name: 'password_hash_algo', type: 'string')]
    private string $passwordHashAlgo = 'argon2id';

    #[ORM\Column(name: 'first_name', type: 'string')]
    private string $firstName;

    #[ORM\Column(name: 'last_name', type: 'string')]
    private string $lastName;

    #[ORM\Column(name: 'is_active', type: 'integer')]
    private int $isActive = 1;

    #[ORM\Column(name: 'is_deleted', type: 'integer')]
    private int $isDeleted = 0;

    #[ORM\Column(type: 'string')]
    private string $role = '';

    #[ORM\Column(name: 'created_at', type: 'datetimetz_immutable')]
    private DateTimeImmutable $createdAt;

    public function id(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function passwordHashAlgo(): string
    {
        return $this->passwordHashAlgo;
    }

    public function setPasswordHashAlgo(string $passwordHashAlgo): void
    {
        $this->passwordHashAlgo = $passwordHashAlgo;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function isActive(): int
    {
        return $this->isActive;
    }

    public function setIsActive(int $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function isDeleted(): int
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(int $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    public function role(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}
