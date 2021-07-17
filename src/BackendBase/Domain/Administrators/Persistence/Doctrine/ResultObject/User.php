<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Persistence\Doctrine\ResultObject;

use JsonSerializable;
use BackendBase\Shared\Persistence\ResultObject;
use DateTimeImmutable;

class User implements JsonSerializable
{
    use ResultObject;

    private string $id;
    private string $email;
    private string $firstName;
    private string $lastName;
    private string $passwordHash;
    private string $passwordHashAlgo;
    private string $role;
    private DateTimeImmutable $createdAt;
    private int $isActive;
    private array $permissions = [];

    public function id(): string
    {
        return $this->id;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }

    public function passwordHashAlgo(): string
    {
        return $this->passwordHashAlgo;
    }

    public function role(): string
    {
        return $this->role;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isActive(): int
    {
        return $this->isActive;
    }

    public function setPermissions(array  $permissions) : void
    {
        $this->permissions = $permissions;
    }

    public function permissions(): int
    {
        return $this->permissions;
    }

}
