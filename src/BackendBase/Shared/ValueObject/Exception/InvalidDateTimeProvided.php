<?php

declare(strict_types=1);

namespace Reformo\Shared\ValueObject\Exception;

use Exception;
use Reformo\Shared\Exception\DomainException;

final class InvalidDateTimeProvided extends Exception
{
    use DomainException;

    private const STATUS = 400;
    private const CODE   = 'localized-datetime/invalid-datetime-format';
    private const TYPE   = 'https://httpstatus.es/400';
    private const TITLE  = 'Invalid datetime format';
}
