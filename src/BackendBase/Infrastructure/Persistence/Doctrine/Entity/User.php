<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Entity;

use DateTimeImmutable;

/**
 * @Entity
 * @Table(name="admin.users")
 */
class User
{
    /**
     * @Id
     * @Column(type="uuid")
     * @GeneratedValue(strategy="NONE")
     */
    protected string $id;

    /** @Column(type="string", name="email") */
    protected string $email;

    /** @Column(type="string", name="password_hash") */
    protected string $passwordHash;

    /** @Column(type="string", name="password_hash_algo") */
    protected string $passwordHashAlgo;

    /** @Column(type="string", name="first_name") */
    protected string $firstName;

    /** @Column(type="string", name="last_name") */
    protected string $lastName;

    /** @Column(type="integer", name="is_active") */
    protected int $isActive = 1;

    /** @Column(type="integer", name="is_deleted") */
    protected int $isDeleted = 0;

    /** @Column(type="string", name="role") */
    protected string $role = '';

    /** @Column(type="datetimetz_immutable", name="created_at") */
    protected DateTimeImmutable $createdAt;

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
