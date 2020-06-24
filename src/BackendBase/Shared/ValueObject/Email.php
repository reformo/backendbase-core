<?php

declare(strict_types=1);

namespace BackendBase\Shared\ValueObject;

use InvalidArgumentException;
use BackendBase\Shared\ValueObject\Exception\InvalidEmailAddress;
use Webmozart\Assert\Assert;

final class Email
{
    private string $email;

    public function __construct(string $email)
    {
        try {
            Assert::email($email);
        } catch (InvalidArgumentException $e) {
            throw InvalidEmailAddress::create('Invalid email address: ' . $email);
        }

        $this->email = $email;
    }
    public static function createFromString(string $email) : self
    {
        return new self($email);
    }

    public function getEmail() : string
    {
        return $this->toString();
    }

    public function toString() : string
    {
        return $this->email;
    }
}
