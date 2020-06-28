<?php

declare(strict_types=1);

namespace BackendBase\Shared\Factory;

use BackendBase\Shared\Middleware\LoggingErrorListener;
use Mezzio\ProblemDetails\ProblemDetailsMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class LoggingErrorListenerDelegatorFactory
{
    public function __invoke(ContainerInterface $container, string $name, callable $callback) : ProblemDetailsMiddleware
    {
        $listener     = new LoggingErrorListener($container->get(LoggerInterface::class));
        $errorHandler = $callback();
        $errorHandler->attachListener($listener);

        return $errorHandler;
    }
}
