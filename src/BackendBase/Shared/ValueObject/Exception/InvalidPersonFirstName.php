<?php

declare(strict_types=1);

namespace BackendBase\Shared\ValueObject\Exception;

use BackendBase\Domain\Shared\Exception\DomainException;
use Exception;

final class InvalidPersonFirstName extends Exception
{
    use DomainException;

    private const STATUS = 400;
    private const CODE   = 'person/invalid-first-name';
    private const TYPE   = 'https://httpstatus.es/400';
    private const TITLE  = 'Invalid person first name';
}
