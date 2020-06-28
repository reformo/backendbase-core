<?php

declare(strict_types=1);

namespace BackendBase\Shared\ValueObject\Exception;

use BackendBase\Shared\Exception\DomainException;
use Exception;

final class InvalidCompanyLegalName extends Exception
{
    use DomainException;

    private const STATUS = 400;
    private const CODE   = 'company-info/invalid-legal-name';
    private const TYPE   = 'https://httpstatus.es/400';
    private const TITLE  = 'Invalid company legal name';
}
