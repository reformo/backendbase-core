<?php

declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

use BackendBase\Infrastructure\Persistence\Doctrine\Repository\RolesRepository;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Http\Server\MiddlewareInterface;

final class PrivateApiAuthorizationMiddlewareFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null) : MiddlewareInterface
    {
        return new PrivateApiAuthorizationMiddleware($container->get(RolesRepository::class), $container->get('config'));
    }
}
