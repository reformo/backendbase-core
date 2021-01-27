<?php

declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Http\Server\MiddlewareInterface;

final class LanguageSelectorMiddlewareFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): MiddlewareInterface
    {
        return new LanguageSelectorMiddleware($container->get('config'));
    }
}
