<?php

declare(strict_types=1);

namespace BackendBase\Shared\ValueObject\Exception;

use BackendBase\Domain\Shared\Exception\DomainException;
use Exception;

final class InvalidCompanyShortName extends Exception
{
    use DomainException;

    private const STATUS = 400;
    private const CODE   = 'company-info/invalid-short-name';
    private const TYPE   = 'https://httpstatus.es/400';
    private const TITLE  = 'Invalid company short name';
}
