<?php

declare(strict_types=1);

namespace BackendBase\Domain\IdentityAndAccess\Model;

use BackendBase\Shared\ValueObject\Interfaces\Email;

class ContactInformation
{
    private Email $email;
    private string $mobile;

    public function __construct(Email $email, string $mobile)
    {
        $this->email  = $email;
        $this->mobile = $mobile;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function mobile(): string
    {
        return $this->mobile;
    }
}
