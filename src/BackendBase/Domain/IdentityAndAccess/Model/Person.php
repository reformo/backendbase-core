<?php

declare(strict_types=1);

namespace BackendBase\Domain\IdentityAndAccess\Model;

use BackendBase\Domain\IdentityAndAccess\Exception\InvalidArgumentException;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException as AssertInvalidArgumentException;

use function implode;
use function str_replace;

class Person
{
    private string $firstName;
    private string $lastName;

    private const NAME_MIN_LENGTH      = 2;
    private const REGEX_IS_LATIN_ALPHA = '/^\p{L}+$/u';

    private static array $exceptionMessages = [
        'firstNameMinLength' => 'First name must be at least ' . self::NAME_MIN_LENGTH . ' characters long',
        'firstNameAlpha' => 'First name must be all alphabetical characters',
        'lastNameMinLength' => 'Last name must be at least ' . self::NAME_MIN_LENGTH . ' characters long',
        'lastNameAlpha' => 'Last name must be all alphabetical characters',
    ];

    public function __construct(string $firstName, string $lastName, private ContactInformation $contactInformation)
    {
        $this->setName($firstName, $lastName);
    }

    private function setName(string $firstName, string $lastName): void
    {
        try {
            Assert::minLength($firstName, self::NAME_MIN_LENGTH, 'firstNameMinLength');
            Assert::regex(str_replace(' ', '', $firstName), self::REGEX_IS_LATIN_ALPHA, 'firstNameAlpha');
            Assert::minLength($lastName, self::NAME_MIN_LENGTH, 'lastNameMinLength');
            Assert::regex(str_replace(' ', '', $lastName), self::REGEX_IS_LATIN_ALPHA, 'lastNameAlpha');
        } catch (AssertInvalidArgumentException $exception) {
            throw InvalidArgumentException::create(
                self::$exceptionMessages[$exception->getMessage()] ?? $exception->getMessage(),
            );
        }

        $this->firstName = $firstName;
        $this->lastName  = $lastName;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function fullName(): string
    {
        return implode(' ', [$this->firstName, $this->lastName]);
    }

    public function contactInformation(): ContactInformation
    {
        return $this->contactInformation;
    }

    public function rename(string $firstName, string $lastname): self
    {
        $newPerson = clone $this;
        $newPerson->setName($firstName, $lastname);

        return $newPerson;
    }

    public function changeContactInformation(ContactInformation $contactInformation): self
    {
        $newPerson                     = clone $this;
        $newPerson->contactInformation = $contactInformation;

        return $newPerson;
    }
}
