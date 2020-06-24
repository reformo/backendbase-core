<?php

declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

use BackendBase\Domain\IdentityAndAccess\Exception\AuthenticationFailed;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\RolesRepository;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use BackendBase\Shared\Services\RoleBasedAccessControl;
use Throwable;
use function str_replace;

final class PrivateApiAuthorizationMiddleware implements MiddlewareInterface
{

    private RolesRepository $rolesRepository;

    public function __construct(RolesRepository $rolesRepository)
    {
        $this->rolesRepository = $rolesRepository;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        if ($request->getMethod() === 'OPTIONS' ||  $request->getUri()->getPath() === '/_reset' || ($request->getMethod() === 'POST' && $request->getUri()->getPath() === '/sessions')) {
            return $handler->handle($request);
        }
        $authHeader = str_replace('Bearer ', '', $request->getHeaderLine('Authorization'));
        if (empty($authHeader)) {
            throw AuthenticationFailed::create('Authentication failed.');
        }
        try {
            $token = (new Parser())->parse((string) $authHeader);
            $data  = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
            $data->setIssuer('storage');
            if ($token->validate($data) === false) {
                throw AuthenticationFailed::create('Authentication failed. Invalid Token or token expired.');
            }
            $userId  = $token->getClaim('userId');
            $roleName    = $token->getClaim('role');

            $role = RoleBasedAccessControl::fromPermissions($roleName, $this->rolesRepository->getRolePermissionsByRoleNameForUser($userId));

            $request = $request
                ->withAttribute('loggedUserId', $userId);
            $request = $request
                ->withAttribute('role', $role);
        } catch (Throwable $e) {
            throw AuthenticationFailed::create('Authentication failed.' .$e->getMessage());
        }

        return $handler->handle($request);
    }
}
