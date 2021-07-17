<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Model;

use BackendBase\Domain\Administrators\Exception\InvalidFirstName;
use BackendBase\Domain\Administrators\Interfaces\UserId as UserIdInterface;
use BackendBase\Shared\ValueObject\Interfaces\Email as EmailInterface;
use BackendBase\Shared\ValueObject\Email;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Throwable;
use Webmozart\Assert\Assert;

use function password_verify;

class User
{
    public const CREATED_AT_FORMAT = 'Y-m-d H:i:s';

    private UserIdInterface $id;
    private EmailInterface $email;
    private string $firstName;
    private string $lastName;
    private string $passwordHash;
    private string $role;
    private DateTimeImmutable $createdAt;

    private function __construct(
        UserIdInterface $id,
        EmailInterface $email,
        string $firstName,
        string $lastName,
        string $passwordHash,
        string $role,
        DateTimeImmutable $createdAt
    ) {
        $this->id           = $id;
        $this->email        = $email;
        $this->firstName    = $firstName;
        $this->lastName     = $lastName;
        $this->passwordHash = $passwordHash;
        $this->role         =  $role;
        $this->lastName     = $lastName;
        $this->createdAt    = $createdAt;
    }

    public static function create(string $uuid, string $email, string $firstName, string $lastName, string $passwordHash, string $role, string $createdAt)
    {
        try {
            Assert::minLength($firstName, 2, 'First name must be at least 2 characters long');
        } catch (Throwable $exception) {
            throw InvalidFirstName::create($exception->getMessage());
        }

        try {
            Assert::minLength($lastName, 2, 'Last name must be at least 2 characters long');
        } catch (Throwable $exception) {
            throw InvalidFirstName::create($exception->getMessage());
        }

        return new static(
            UserId::createFromString($uuid),
            Email::createFromString($email),
            $firstName,
            $lastName,
            $passwordHash,
            $role,
            CarbonImmutable::parse($createdAt)->toDateTimeImmutable()
        );
    }

    public function id(): UserIdInterface
    {
        return $this->id;
    }

    public function email(): Email
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

    public function role(): string
    {
        return $this->role;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }
}
