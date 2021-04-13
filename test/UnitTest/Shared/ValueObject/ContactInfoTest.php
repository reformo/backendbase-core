<?php

declare(strict_types=1);

namespace UnitTest\Shared\ValueObject;

use BackendBase\Shared\ValueObject\ContactInfo;
use BackendBase\Shared\ValueObject\Email;
use BackendBase\Shared\ValueObject\PhoneNumber;
use PHPUnit\Framework\TestCase;

final class ContactInfoTest extends TestCase
{
    /**
     * @test
     */
    public function shouldSuccessfullyInit(): void
    {
        $contactInfo = new ContactInfo(
            new Email('mehmet@mkorkmaz.com'),
            PhoneNumber::fromString('+90 555 555 55 55')
        );
        $this->assertSame('mehmet@mkorkmaz.com', $contactInfo->email()->toString());
        $this->assertSame('+905555555555', $contactInfo->phoneNumber()->getE164FormattedNumber());
    }
}
