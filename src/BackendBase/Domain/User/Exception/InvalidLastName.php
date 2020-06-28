<?php

declare(strict_types=1);

namespace BackendBase\Domain\User\Exception;

use BackendBase\Domain\Shared\Exception\DomainException;
use Exception;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;

class InvalidLastName extends Exception implements ProblemDetailsExceptionInterface
{
    use DomainException;

    protected const STATUS = 400;
    protected const CODE   = 'USER-1002';
    protected const TYPE   = 'https://httpstatus.es/400';
    protected const TITLE  = 'Invalid last name';
}
