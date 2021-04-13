<?php

declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Redis;

use function count;
use function explode;
use function in_array;
use function json_decode;
use function json_encode;
use function md5;

use const JSON_THROW_ON_ERROR;

class CacheAndServePagesMiddleware implements MiddlewareInterface
{
    private Redis $redis;
    private array $config;

    public function __construct(Redis $redis, array $config)
    {
        $this->redis  = $redis;
        $this->config = $config;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path      = $request->getUri()->getPath();
        $pathParts = explode('/', $path);
        if (
            $request->getMethod() === 'GET'
            && ! in_array($pathParts[1], $this->config['app']['page-cache-exclude'] ?? [])
            && count($request->getQueryParams()) === 0
        ) {
            $key   = 'backendbase-page-' . md5($path);
            $cache = $this->redis->get($key);
            if (empty($cache)) {
                $response = $handler->handle($request);
                if ($response instanceof HtmlResponse) {
                    $responseData = [
                        'html' => $response->getBody()->getContents(),
                        'headers' => $response->getHeaders(),
                        'statusCode' => $response->getStatusCode(),
                    ];
                    $this->redis->set($key, json_encode($responseData, JSON_THROW_ON_ERROR), $this->config['app']['page-cache-ttl'] ?? 60);
                }

                return $response->withHeader('CACHE-HIT', 0);
            }

            $responseData = json_decode($cache, true, 512, JSON_THROW_ON_ERROR);

            return (new HtmlResponse($responseData['html'], $responseData['statusCode'], $responseData['headers']))->withHeader('CACHE-HIT', 1);
        }

        return $handler->handle($request);
    }
}
