<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Model;

use BackendBase\Domain\Administrators\Interfaces\UserId as UserIdInterface;
use BackendBase\Domain\IdentityAndAccess\Model\ContactInformation;
use BackendBase\Domain\IdentityAndAccess\Model\Person;
use BackendBase\Shared\ValueObject\Email;
use BackendBase\Shared\ValueObject\ObjectSerializer;
use DateTimeImmutable;
use JsonSerializable;

class User implements JsonSerializable
{
    use ObjectSerializer;

    public const CREATED_AT_FORMAT           = 'Y-m-d H:i:s';
    public const IS_ACTIVE_DEFAULT_ON_CREATE = true;

    private UserIdInterface $id;
    private Person $person;
    private string $passwordHash;
    private string $role;
    private bool $isActive;
    private DateTimeImmutable $createdAt;

    private function __construct(
        UserIdInterface $id,
        Person $person,
        string $passwordHash,
        string $role,
        bool $isActive,
        DateTimeImmutable $createdAt
    ) {
        $this->id           = $id;
        $this->person       = $person;
        $this->passwordHash = $passwordHash;
        $this->role         =  $role;
        $this->createdAt    = $createdAt;
        $this->isActive     = $isActive;
    }

    public static function new(string $uuid, string $email, string $firstName, string $lastName, string $passwordHash, string $role, DateTimeImmutable $createdAt)
    {
        return new static(
            UserId::createFromString($uuid),
            new Person($firstName, $lastName, new ContactInformation(Email::createFromString($email))),
            $passwordHash,
            $role,
            self::IS_ACTIVE_DEFAULT_ON_CREATE,
            $createdAt
        );
    }

    public static function create(string $uuid, string $email, string $firstName, string $lastName, string $passwordHash, string $role, int $isActive, DateTimeImmutable $createdAt)
    {
        return new static(
            UserId::createFromString($uuid),
            new Person($firstName, $lastName, new ContactInformation(Email::createFromString($email))),
            $passwordHash,
            $role,
            (bool) $isActive,
            $createdAt
        );
    }

    public function id(): UserIdInterface
    {
        return $this->id;
    }

    public function email(): Email
    {
        return $this->person()
            ->contactInformation()
            ->email();
    }

    public function person(): Person
    {
        return $this->person;
    }

    public function firstName(): string
    {
        return $this->person->firstName();
    }

    public function lastName(): string
    {
        return $this->person->lastName();
    }

    public function fullName(): string
    {
        return $this->person->fullName();
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }

    public function role(): string
    {
        return $this->role;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
