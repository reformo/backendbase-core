<?php

declare(strict_types=1);

namespace Reformo\Shared\ValueObject;

final class ContactInfo
{
    private Email $email;
    private PhoneNumber $phoneNumber;

    public function __construct(Email $email, PhoneNumber $phoneNumber)
    {
        $this->email       = $email;
        $this->phoneNumber = $phoneNumber;
    }

    public function getEmail() : Email
    {
        return $this->email;
    }

    public function getPhoneNumber() : PhoneNumber
    {
        return $this->phoneNumber;
    }
}
