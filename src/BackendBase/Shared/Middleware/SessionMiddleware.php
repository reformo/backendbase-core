<?php

declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

use BackendBase\Shared\Services\FlashMessages;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Redis;
use Ulid\Ulid;

class SessionMiddleware implements MiddlewareInterface
{
    public const SESSION_ID_KEY    =  'sessionId';
    public const SESSION_ATTRIBUTE = 'session';
    private Redis $redis;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /**
         * @var $session SessionInterface;
         */
        $session   = $request->getAttribute(self::SESSION_ATTRIBUTE);
        $sessionId = $session->get(self::SESSION_ID_KEY);
        if ($sessionId === null) {
            $sessionId = (string) Ulid::generate(true);
            $session->set(self::SESSION_ID_KEY, $sessionId);
        }

        $request = $request->withAttribute(self::SESSION_ID_KEY, $sessionId)
        ->withAttribute(FlashMessages::FLASH_MESSAGE_ATTRIBUTE, new FlashMessages($this->redis, $sessionId));

        return $handler->handle($request);
    }
}
