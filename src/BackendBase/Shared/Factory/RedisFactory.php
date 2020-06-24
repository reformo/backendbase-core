<?php

declare(strict_types=1);

namespace BackendBase\Shared\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Redis;

final class RedisFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null) : Redis
    {
        $config      = $container->get('config');
        $redisClient = new Redis();
        $redisClient->connect($config['redis']['host']);

        return $redisClient;
    }
}
