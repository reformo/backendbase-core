<?php

declare(strict_types=1);

namespace BackendBase\Domain\User\Exception;

use Exception;
use BackendBase\Domain\Shared\Exception\DomainException;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;

class CantUnregisterUserDoesNotExists extends Exception implements ProblemDetailsExceptionInterface
{
    use DomainException;

    private const STATUS = 410;
    private const CODE   = 'USER-1000';
    private const TYPE   = 'https://httpstatus.es/404';
    private const TITLE  = 'User to be unregister can not be found';
}
