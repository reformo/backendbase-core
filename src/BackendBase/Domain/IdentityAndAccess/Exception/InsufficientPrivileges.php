<?php

declare(strict_types=1);

namespace BackendBase\Domain\IdentityAndAccess\Exception;

use Exception;
use BackendBase\Domain\Shared\Exception\DomainException;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;

class InsufficientPrivileges extends Exception implements ProblemDetailsExceptionInterface
{
    use DomainException;

    private const STATUS = 403;
    private const CODE   = 'identity-and-access/insufficient-privileges';
    private const TYPE   = 'https://httpstatus.es/403';
    private const TITLE  = 'Insufficient privileges';
}
