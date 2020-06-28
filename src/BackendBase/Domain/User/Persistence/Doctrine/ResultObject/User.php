<?php

declare(strict_types=1);

namespace BackendBase\Domain\User\Persistence\Doctrine\ResultObject;

use BackendBase\Shared\Services\Persistence\ResultObject;
use JsonSerializable;

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
    private string $roleStr;
    private string $createdAt;
    private int $isActive;

    public function id() : string
    {
        return $this->id;
    }

    public function email() : string
    {
        return $this->email;
    }

    public function firstName() : string
    {
        return $this->firstName;
    }

    public function lastName() : string
    {
        return $this->lastName;
    }

    public function passwordHash() : string
    {
        return $this->passwordHash;
    }

    public function role() : string
    {
        return $this->role;
    }

    public function roleStr() : string
    {
        return $this->roleStr;
    }

    public function createdAt() : string
    {
        return $this->createdAt;
    }
}
