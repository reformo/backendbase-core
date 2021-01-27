<?php

declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;

use function json_decode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

class LoggingErrorListener
{
    /**
     * Log format for messages:
     *
     * STATUS [METHOD] path: message
     */
    public const LOG_FORMAT = '%d [%s] %s: %s';

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(Throwable $error, ServerRequestInterface $request, ResponseInterface $response): void
    {
        if ($response->getStatusCode() < 500) {
            return;
        }

        $this->logger->error(sprintf(
            self::LOG_FORMAT,
            $response->getStatusCode(),
            $request->getMethod(),
            (string) $request->getUri(),
            $error->getMessage()
        ), json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR));
    }
}
