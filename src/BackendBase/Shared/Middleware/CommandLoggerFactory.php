<?php

declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

use Doctrine\DBAL\Connection;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Http\Server\MiddlewareInterface;

final class CommandLoggerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): MiddlewareInterface
    {
        $doctrineDbal = $container->get(Connection::class);

        return new CommandLogger($doctrineDbal);
    }
}
