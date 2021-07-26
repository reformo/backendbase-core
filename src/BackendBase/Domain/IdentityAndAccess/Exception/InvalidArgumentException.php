<?php

declare(strict_types=1);

namespace BackendBase\Domain\IdentityAndAccess\Exception;

use BackendBase\Domain\Shared\Exception\DomainException;
use Exception;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;

class InvalidArgumentException extends Exception implements ProblemDetailsExceptionInterface
{
    use DomainException;

    private const STATUS = 400;
    private const CODE   = 'users/invalid-argument';
    private const TYPE   = 'https://httpstatus.es/400';
    private const TITLE  = 'Invalid Argument';
}
