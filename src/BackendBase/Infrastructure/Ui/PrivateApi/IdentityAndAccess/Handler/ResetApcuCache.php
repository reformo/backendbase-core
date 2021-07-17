<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\IdentityAndAccess\Handler;

use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function apcu_cache_info;
use function apcu_clear_cache;

class ResetApcuCache implements RequestHandlerInterface
{
    private array $config;
    public function __construct(array $config)
    {
        $this->config          = $config;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $apiKey = $request->getHeaderLine('BackendBase-Api-Key');
        if ($apiKey === $this->config['app']['api-key']) {
            $before = apcu_cache_info();
            apcu_clear_cache();
            $after = apcu_cache_info();

            return new JsonResponse(['before' => $before, 'after' => $after]);
        }

        return new EmptyResponse(403);
    }
}
