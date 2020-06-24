<?php

declare(strict_types=1);

namespace BackendBase\Domain\User\Exception;

use Exception;
use BackendBase\Domain\Shared\Exception\DomainException;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;

class UserAlreadyExists extends Exception implements ProblemDetailsExceptionInterface
{
    use DomainException;

    private const STATUS = 409;
    private const CODE   = 'users/user-exists';
    private const TYPE   = 'https://httpstatus.es/409';
    private const TITLE  = 'User Exists';
}
