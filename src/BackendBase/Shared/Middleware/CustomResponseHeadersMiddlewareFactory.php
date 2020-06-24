<?php

declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Http\Server\MiddlewareInterface;

final class CustomResponseHeadersMiddlewareFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null) : MiddlewareInterface
    {
        $config        = $container->get('config');
        $customHeaders = $config['http']['response'] ?? [ 'custom-headers' => [], 'allow-origins' => []];

        return new CustomResponseHeadersMiddleware($customHeaders['custom-headers'], $customHeaders['allow-origins']);
    }
}
