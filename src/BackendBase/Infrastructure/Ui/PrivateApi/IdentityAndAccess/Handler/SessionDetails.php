<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\IdentityAndAccess\Handler;

use BackendBase\Domain\Administrators\Query\GetUserById;
use BackendBase\Shared\CQRS\Interfaces\QueryBus;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionDetails implements RequestHandlerInterface
{
    public function __construct(private QueryBus $queryBus)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $message = new GetUserById($request->getAttribute('loggedUserId'));
        $user    = $this->queryBus->handle($message);
        $data    = ['user' => $user];

        return new JsonResponse($data, 200);
    }
}
