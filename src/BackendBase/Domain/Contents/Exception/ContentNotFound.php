<?php

declare(strict_types=1);

namespace BackendBase\Domain\Contents\Exception;

use Exception;
use BackendBase\Domain\Shared\Exception\DomainException;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;

class ContentNotFound extends Exception implements ProblemDetailsExceptionInterface
{
    use DomainException;

    private const STATUS = 404;
    private const CODE   = 'contents/not-found';
    private const TYPE   = 'https://httpstatus.es/404';
    private const TITLE  = 'Content Not Found';
}
