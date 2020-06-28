<?php

declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Driver\Connection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;
use const DATE_ATOM;
use const JSON_THROW_ON_ERROR;
use function array_key_exists;
use function in_array;
use function json_decode;
use function json_encode;
use function stripos;
use function strpos;

class CommandLogger implements MiddlewareInterface
{
    private Connection $doctrineDbal;

    public function __construct(Connection $doctrineDbal)
    {
        $this->doctrineDbal = $doctrineDbal;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        if (! in_array($request->getMethod(), ['POST', 'PATCH', 'PUT', 'DELETE'])) {
            return $handler->handle($request);
        }
        $response     = $handler->handle($request);
        $parsedBody   = $request->getParsedBody() ?? [];
        $attributes   = $request->getAttributes();
        $acceptHeader = $request->getHeaderLine('Accept');
        $source       = 'unknown';
        $apiVersion   = 'unknown';
        if (strpos($acceptHeader, 'private-backendbase') !== false) {
            $source     = 'private-api';
            $apiVersion = $acceptHeader;
        }
        if (strpos($acceptHeader, 'public-backendbase') !== false) {
            $source     = 'public-web';
            $apiVersion = $acceptHeader;
        }
        if (array_key_exists('password', $parsedBody)) {
            $parsedBody['password'] = '**********';
        }
        unset($attributes['rawBody']);
        $serverParamsData = $request->getServerParams();
        $serverParams     = [];
        foreach ($serverParamsData as $param => $value) {
            $serverParams[$param] = $value;
            if (stripos($param, 'PASSWORD') === false) {
                continue;
            }

            $serverParams[$param] = '********';
        }
        $requestData  = [
            'method' => $request->getMethod(),
            'endPoint' => $request->getUri()->getPath(),
            'queryParams' => $request->getQueryParams(),
            'parsedBody' => $parsedBody,
            'headers' => $request->getHeaders(),
            'environment' => $serverParams,
            'attributes' => $attributes,
        ];
        $responseBody = (string) $response->getBody();
        $responseData = [
            'status' => $response->getStatusCode(),
            'response' => json_decode($responseBody !== '' ? $responseBody : '{}', true, 512, JSON_THROW_ON_ERROR),
            'reasonPhrase' => $response->getReasonPhrase(),
        ];
        $logData      = [
            'id' => Uuid::uuid4()->toString(),
            'user_id' => $request->getAttribute('loggedUserId'),
            'source' => $source,
            'api_version' => $apiVersion,
            'request' => json_encode($requestData),
            'response' => json_encode($responseData),
            'logged_at' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format(DATE_ATOM),
        ];
        $this->doctrineDbal->insert('admin.command_logs', $logData);

        return $response;
    }
}
