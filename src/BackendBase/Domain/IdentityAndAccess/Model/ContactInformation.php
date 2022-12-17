<?php

declare(strict_types=1);

namespace BackendBase\Domain\IdentityAndAccess\Model;

use BackendBase\Shared\ValueObject\Interfaces\Email;
use BackendBase\Shared\ValueObject\PhoneNumber;

class ContactInformation
{
    public function __construct(private Email $email, private ?\BackendBase\Shared\ValueObject\PhoneNumber $mobile = null)
    {
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function mobile(): ?PhoneNumber
    {
        return $this->mobile;
    }
}
