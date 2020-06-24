<?php

declare(strict_types=1);

namespace Reformo\Shared\ValueObject;

use InvalidArgumentException;
use Reformo\Shared\ValueObject\Exception\InvalidPersonFamilyName;
use Reformo\Shared\ValueObject\Exception\InvalidPersonFirstName;
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
        } catch (InvalidArgumentException $e) {
            throw InvalidPersonFirstName::create(
                'Name must be at least 1 character long',
                ['error' => 'person/invalid-first-name']
            );
        }

        try {
            Assert::minLength($familyName, 2);
        } catch (InvalidArgumentException $e) {
            throw InvalidPersonFamilyName::create(
                'Family name must be at least 2 character long',
                ['error' => 'person/invalid-family-name']
            );
        }

        $this->firstName  = $firstName;
        $this->familyName = $familyName;
    }

    public static function fromFullName(string $fullName) : self
    {
        $names      = explode(' ', $fullName);
        $familyName = array_pop($names);

        return new self(implode(' ', $names), $familyName);
    }

    public function getFirstName() : string
    {
        return $this->firstName;
    }

    public function getFamilyName() : string
    {
        return $this->familyName;
    }

    public function getFullName() : string
    {
        return $this->firstName . ' ' . $this->familyName;
    }
}
