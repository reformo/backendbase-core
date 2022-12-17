<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\IdentityAndAccess\Handler;

use BackendBase\Domain\Administrators\Interfaces\UserRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\User;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Selami\Stdlib\Arrays\PayloadSanitizer;

class ChangeMyDetails implements RequestHandlerInterface
{
    public function __construct(private UserRepository $userRepository, private GenericRepository $genericRepository)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $loggedUserId = $request->getAttribute('loggedUserId');
        $payload      = PayloadSanitizer::sanitize($request->getParsedBody());
        unset($payload['role']);
        $this->genericRepository->updateGeneric(User::class, $loggedUserId, $payload);

        return new EmptyResponse(204);
    }
}
