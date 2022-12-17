<?php

declare(strict_types=1);

namespace BackendBase\Shared\ValueObject;

use BackendBase\Shared\ValueObject\Exception\InvalidPersonFamilyName;
use BackendBase\Shared\ValueObject\Exception\InvalidPersonFirstName;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

use function array_pop;
use function explode;
use function implode;

class Person
{
    private string $firstName;
    private string $familyName;

    public function __construct(string $firstName, string $familyName)
    {
        try {
            Assert::minLength($firstName, 1);
        } catch (InvalidArgumentException) {
            throw InvalidPersonFirstName::create(
                'Name must be at least 1 character long',
                ['error' => 'person/invalid-first-name']
            );
        }

        try {
            Assert::minLength($familyName, 2);
        } catch (InvalidArgumentException) {
            throw InvalidPersonFamilyName::create(
                'Family name must be at least 2 character long',
                ['error' => 'person/invalid-family-name']
            );
        }

        $this->firstName  = $firstName;
        $this->familyName = $familyName;
    }

    public static function fromFullName(string $fullName): self
    {
        $names      = explode(' ', $fullName);
        $familyName = array_pop($names);

        return new self(implode(' ', $names), $familyName);
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function familyName(): string
    {
        return $this->familyName;
    }

    public function fullName(): string
    {
        return $this->firstName . ' ' . $this->familyName;
    }
}
