<?php

declare(strict_types=1);

namespace BackendBase\Shared\ValueObject\Exception;

use BackendBase\Shared\Exception\DomainException;
use Exception;

final class InvalidPhoneNumber extends Exception
{
    use DomainException;

    private const STATUS = 400;
    private const CODE   = 'email/invalid-phone-number';
    private const TYPE   = 'https://httpstatus.es/400';
    private const TITLE  = 'Invalid phone number';
}
