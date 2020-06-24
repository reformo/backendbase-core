<?php

declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function in_array;

class CustomResponseHeadersMiddleware implements MiddlewareInterface
{
    private array $defaultHeaders;
    private array $allowOrigins;

    public function __construct(array $defaultHeaders, array $allowOrigins)
    {
        $this->defaultHeaders = $defaultHeaders;
        $this->allowOrigins   = $allowOrigins;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $response = $handler->handle($request);
        foreach ($this->defaultHeaders as $customHeader => $customHeaderValue) {
            $response = $response->withHeader($customHeader, $customHeaderValue);
        }
        $origin = $request->getHeader('Origin')[0] ?? 'no-origin';

        if (in_array($origin, $this->allowOrigins, true)) {
            return $response->withHeader('Access-Control-Allow-Origin', $origin);
        }
        if (in_array('*', $this->allowOrigins, true)) {
            return $response->withHeader('Access-Control-Allow-Origin', '*');
        }

        return $response;
    }
}
