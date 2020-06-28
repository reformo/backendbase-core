<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\IdentityAndAccess\Handler;

use BackendBase\Domain\User\Interfaces\UserRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\User;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use BackendBase\Shared\Services\PayloadSanitizer;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ChangeMyDetails implements RequestHandlerInterface
{
    private UserRepository $userRepository;
    private GenericRepository $genericRepository;

    public function __construct(
        UserRepository $userRepository,
        GenericRepository $genericRepository
    ) {
        $this->userRepository    = $userRepository;
        $this->genericRepository = $genericRepository;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $loggedUserId = $request->getAttribute('loggedUserId');
        $payload      = PayloadSanitizer::sanitize($request->getParsedBody());
        unset($payload['role']);
        $this->genericRepository->updateGeneric(User::class, $loggedUserId, $payload);

        return new EmptyResponse(204);
    }
}
