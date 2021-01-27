<?php

declare(strict_types=1);

namespace BackendBase\Shared\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use RateLimit\RedisRateLimiter;
use Redis;

final class RedisRateLimiterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): RedisRateLimiter
    {
        $redisClient = $container->get(Redis::class);

        return new RedisRateLimiter($redisClient, 'RT-');
    }
}
