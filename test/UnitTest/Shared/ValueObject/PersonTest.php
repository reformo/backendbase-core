<?php

declare(strict_types=1);

namespace UnitTest\Shared\ValueObject;

use BackendBase\Shared\ValueObject\Exception\InvalidPersonFamilyName;
use BackendBase\Shared\ValueObject\Exception\InvalidPersonFirstName;
use BackendBase\Shared\ValueObject\Person;
use PHPUnit\Framework\TestCase;

final class PersonTest extends TestCase
{
    /**
     * @test
     */
    public function shouldSuccessfullyInit() : void
    {
        $person = Person::fromFullName('Theodor Seuss Ted Geisel');
        $this->assertSame('Theodor Seuss Ted', $person->firstName());
        $this->assertSame('Geisel', $person->familyName());
        $this->assertSame('Theodor Seuss Ted Geisel', $person->fullName());
    }

    /**
     * @test
     */
    public function shouldFailForInvalidFirstName() : void
    {
        $this->expectException(InvalidPersonFirstName::class);
        Person::fromFullName('Seuss');
    }

    /**
     * @test
     */
    public function shouldFailForInvalidLastName() : void
    {
        $this->expectException(InvalidPersonFamilyName::class);
        Person::fromFullName('Seuss ');
    }
}
