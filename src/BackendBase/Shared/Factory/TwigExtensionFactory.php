<?php

declare(strict_types=1);

namespace BackendBase\Shared\Factory;

use BackendBase\Shared\Services\TwigExtension;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class TwigExtensionFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null) : TwigExtension
    {
        return new TwigExtension();
    }
}
