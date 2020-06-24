<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Exception;

use Exception;
use BackendBase\Domain\Shared\Exception\DomainException;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;

class CollectionNotFound extends Exception implements ProblemDetailsExceptionInterface
{
    use DomainException;

    private const STATUS = 404;
    private const CODE   = 'collection/not-found';
    private const TYPE   = 'https://httpstatus.es/404';
    private const TITLE  = 'Collection Not Found';
}
