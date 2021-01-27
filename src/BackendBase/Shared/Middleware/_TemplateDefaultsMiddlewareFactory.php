<?php

declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Mezzio\Template\TemplateRendererInterface;

final class TemplateDefaultsMiddlewareFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): TemplateDefaultsMiddleware
    {
        return new TemplateDefaultsMiddleware($container->get(TemplateRendererInterface::class));
    }
}
