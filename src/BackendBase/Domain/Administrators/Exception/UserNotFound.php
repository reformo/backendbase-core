<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Exception;

use BackendBase\Domain\Shared\Exception\DomainException;
use Exception;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;

class UserNotFound extends Exception implements ProblemDetailsExceptionInterface
{
    use DomainException;

    private const STATUS = 404;
    private const CODE   = 'users/user-not-found';
    private const TYPE   = 'https://httpstatus.es/404';
    private const TITLE  = 'User Not Found';
}
