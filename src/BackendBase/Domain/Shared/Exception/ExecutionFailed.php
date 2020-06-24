<?php

declare(strict_types=1);

namespace BackendBase\Domain\Shared\Exception;

use Exception;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;

class ExecutionFailed extends Exception implements ProblemDetailsExceptionInterface
{
    use DomainException;

    private const STATUS = 500;
    private const CODE   = 'general/server_error';
    private const TITLE  = 'Execution failed.';
}
