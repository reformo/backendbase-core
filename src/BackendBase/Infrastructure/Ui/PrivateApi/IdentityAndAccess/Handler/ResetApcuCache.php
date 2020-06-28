<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\IdentityAndAccess\Handler;

use BackendBase\Domain\User\Interfaces\UserQuery;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\RolesRepository;
use BackendBase\Shared\Services\MessageBus\Interfaces\QueryBus;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function apcu_cache_info;
use function apcu_clear_cache;

class ResetApcuCache implements RequestHandlerInterface
{
    private $config;
    private $queryBus;
    private UserQuery $userQuery;
    private RolesRepository $rolesRepository;

    public function __construct(
        QueryBus $queryBus,
        UserQuery $userQuery,
        RolesRepository $rolesRepository,
        array $config
    ) {
        $this->config          = $config;
        $this->queryBus        = $queryBus;
        $this->userQuery       = $userQuery;
        $this->rolesRepository = $rolesRepository;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $apiKey = $request->getHeaderLine('BackendBase-Api-Key');
        if ($apiKey === $this->config['app']['api-key']) {
            $before = apcu_cache_info();
            apcu_clear_cache();
            $after =apcu_cache_info();

            return new JsonResponse(['before' => $before, 'after' => $after]);
        }

        return new EmptyResponse(403);
    }
}
