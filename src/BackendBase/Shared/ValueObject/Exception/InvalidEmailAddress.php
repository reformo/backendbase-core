<?php

declare(strict_types=1);

namespace BackendBase\Shared\ValueObject\Exception;

use BackendBase\Domain\Shared\Exception\DomainException;
use Exception;

final class InvalidEmailAddress extends Exception
{
    use DomainException;

    private const STATUS = 400;
    private const CODE   = 'email/invalid-email-address';
    private const TYPE   = 'https://httpstatus.es/400';
    private const TITLE  = 'Invalid email address';
}
