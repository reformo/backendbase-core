<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Exception;

use BackendBase\Domain\Shared\Exception\DomainException;
use Exception;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;

class InvalidFirstName extends Exception implements ProblemDetailsExceptionInterface
{
    use DomainException;

    private const STATUS = 400;
    private const CODE   = 'USER-1001';
    private const TYPE   = 'https://httpstatus.es/400';
    private const TITLE  = 'Invalid first name.';
}
