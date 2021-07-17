<?php

declare(strict_types=1);

namespace BackendBase\Shared\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Redis;
use Redislabs\Module\RedisJson\RedisJson;

final class RedisJsonFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): RedisJson
    {
        /**
         * @var Redis
         */
        $redisClient = $container->get(Redis::class);

        return RedisJson::createWithPhpRedis($redisClient);
    }
}
