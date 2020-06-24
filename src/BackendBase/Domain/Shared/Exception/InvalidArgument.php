<?php

declare(strict_types=1);

namespace BackendBase\Domain\Shared\Exception;

use InvalidArgumentException;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;

class InvalidArgument extends InvalidArgumentException implements ProblemDetailsExceptionInterface
{
    use DomainException;

    private const STATUS = 400;
    private const CODE   = 'general/invalid_parameter';
    private const TYPE   = 'https://httpstatus.es/400';
    private const TITLE  = 'Invalid parameter.';
}
