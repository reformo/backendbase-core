<?php

declare(strict_types=1);

namespace BackendBase\Domain\IdentityAndAccess\Model;

use BackendBase\Shared\ValueObject\Interfaces\Email;
use BackendBase\Shared\ValueObject\PhoneNumber;

class ContactInformation
{
    private Email $email;
    private ?PhoneNumber $mobile;

    public function __construct(Email $email, ?PhoneNumber $mobile = null)
    {
        $this->email  = $email;
        $this->mobile = $mobile;
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
