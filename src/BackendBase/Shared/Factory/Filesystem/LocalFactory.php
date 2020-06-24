<?php

declare(strict_types=1);

namespace BackendBase\Shared\Factory\Filesystem;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

final class LocalFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $config  = $container->get('config');
        $adapter = new Local($config['app']['storage-dir']);

        return new Filesystem($adapter);
    }
}
