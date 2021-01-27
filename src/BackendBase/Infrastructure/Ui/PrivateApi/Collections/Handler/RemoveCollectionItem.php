<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Collections\Handler;

use BackendBase\Shared\Services\MessageBus\Interfaces\QueryBus;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RemoveCollectionItem implements RequestHandlerInterface
{
    private $config;
    private $queryBus;

    public function __construct(
        QueryBus $queryBus,
        array $config
    ) {
        $this->config   = $config;
        $this->queryBus = $queryBus;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
       /* $query = new GetAllUsers(0, 25);
        $users = $this->queryBus->handle($query);
        */
        $session = [
            'access_token' => '',
            'will_expire' => '',
        ];

        return new JsonResponse(['session' => $session], 201);
    }
}
