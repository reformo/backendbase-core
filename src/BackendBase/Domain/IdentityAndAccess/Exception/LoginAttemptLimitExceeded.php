<?php

declare(strict_types=1);

namespace BackendBase\Domain\IdentityAndAccess\Exception;

use Exception;
use BackendBase\Domain\Shared\Exception\DomainException;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;

class LoginAttemptLimitExceeded extends Exception implements ProblemDetailsExceptionInterface
{
    use DomainException;

    private const STATUS = 429;
    private const CODE   = 'identity-and-access/login-attempt-limit-exceeded';
    private const TYPE   = 'https://httpstatus.es/429';
    private const TITLE  = 'Number of login attempt limit exceeded';
}
