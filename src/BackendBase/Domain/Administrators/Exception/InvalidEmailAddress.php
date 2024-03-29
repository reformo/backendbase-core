<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Exception;

use BackendBase\Domain\Shared\Exception\DomainException;
use Exception;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;

class InvalidEmailAddress extends Exception implements ProblemDetailsExceptionInterface
{
    use DomainException;

    protected const STATUS = 400;
    protected const CODE   = 'users/invalid-email';
    protected const TYPE   = 'https://httpstatus.es/400';
    protected const TITLE  = 'Invalid email address';
}
