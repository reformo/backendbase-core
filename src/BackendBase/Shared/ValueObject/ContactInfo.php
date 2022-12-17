<?php

declare(strict_types=1);

namespace BackendBase\Shared\ValueObject;

use BackendBase\Shared\ValueObject\Interfaces\Email;

final class ContactInfo
{
    public function __construct(private Email $email, private ?\BackendBase\Shared\ValueObject\PhoneNumber $phoneNumber)
    {
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
