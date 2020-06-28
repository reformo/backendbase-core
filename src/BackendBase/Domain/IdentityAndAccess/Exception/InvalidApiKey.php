<?php

declare(strict_types=1);

namespace BackendBase\Domain\IdentityAndAccess\Exception;

use BackendBase\Domain\Shared\Exception\DomainException;
use Exception;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;

class InvalidApiKey extends Exception implements ProblemDetailsExceptionInterface
{
    use DomainException;

    private const STATUS = 400;
    private const CODE   = 'identity-and-access/invalid-api-key';
    private const TYPE   = 'https://httpstatus.es/400';
    private const TITLE  = 'Invalid Api Key';
}
