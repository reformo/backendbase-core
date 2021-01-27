<?php

declare(strict_types=1);

namespace BackendBase\Shared\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Redis;
use Redislabs\Module\ReJSON\ReJSON;

final class ReJSONFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ReJSON
    {
        /**
         * @var Redis
         */
        $redisClient = $container->get(Redis::class);

        return ReJSON::createWithPhpRedis($redisClient);
    }
}
