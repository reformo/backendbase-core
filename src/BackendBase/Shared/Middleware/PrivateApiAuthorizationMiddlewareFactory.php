<?php

declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

use Interop\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\RolesRepository;

final class PrivateApiAuthorizationMiddlewareFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null) : MiddlewareInterface
    {
        return new PrivateApiAuthorizationMiddleware($container->get(RolesRepository::class));
    }
}
