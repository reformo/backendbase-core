<?php

declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class TemplateDefaultsMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : TemplateDefaultsMiddleware
    {
        return new TemplateDefaultsMiddleware($container->get(TemplateRendererInterface::class));
    }
}
