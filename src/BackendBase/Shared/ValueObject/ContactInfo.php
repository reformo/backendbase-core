<?php

declare(strict_types=1);

namespace BackendBase\Shared\ValueObject;

use BackendBase\Shared\ValueObject\Interfaces\Email;

final class ContactInfo
{
    private Email $email;
    private ?PhoneNumber $phoneNumber;

    public function __construct(Email $email, ?PhoneNumber $phoneNumber)
    {
        $this->email       = $email;
        $this->phoneNumber = $phoneNumber;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function phoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
    }
}
