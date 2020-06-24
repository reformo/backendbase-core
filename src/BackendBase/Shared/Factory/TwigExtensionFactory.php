<?php

declare(strict_types=1);

namespace BackendBase\Shared\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use BackendBase\Shared\Services\TwigExtension;

class TwigExtensionFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new TwigExtension();
    }
}
