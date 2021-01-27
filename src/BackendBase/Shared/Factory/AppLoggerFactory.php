<?php

declare(strict_types=1);

namespace BackendBase\Shared\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

final class AppLoggerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): LoggerInterface
    {
        $config = $container->get('config');
        $logger = new Logger($config['logger']['name']);
        $logger->pushHandler(new StreamHandler(
            $config['logger']['StreamHandler']['file_path'],
            $config['logger']['StreamHandler']['level']
        ));

        return $logger;
    }
}
