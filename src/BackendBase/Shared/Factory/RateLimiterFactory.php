<?php

declare(strict_types=1);

namespace BackendBase\Shared\Factory;

use BackendBase\Domain\IdentityAndAccess\Model\Login;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Redis;
use RateLimit\Rate;
use RateLimit\RateLimiter;
use RateLimit\RedisRateLimiter;
use function class_implements;
use function in_array;
use BackendBase\Shared\Services\RateLimits\RateLimit;

class RateLimiterFactory extends ReflectionBasedAbstractFactory
{
    public function canCreate(ContainerInterface $container, $requestedName) : bool
    {
        return class_exists($requestedName) && in_array(RateLimiter::class, class_implements($requestedName), true);
    }

    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): RateLimiter
    {
        $redisClient = $container->get(Redis::class);
        $prefix = 'RT-';
        $redisRateLimiter = new RedisRateLimiter(Rate::{($requestedName)::RATE_LIMIT_WINDOW}(($requestedName)::RATE_LIMIT_COUNT), $redisClient, $prefix);

        return ($requestedName)::create($redisRateLimiter);

    }
}
