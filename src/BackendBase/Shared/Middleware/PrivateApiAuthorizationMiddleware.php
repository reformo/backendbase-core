<?php

declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

use BackendBase\Domain\IdentityAndAccess\Exception\AuthenticationFailed;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\RolesRepository;
use BackendBase\Shared\Services\RoleBasedAccessControl;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\IdentifiedBy;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use Lcobucci\Clock\SystemClock;

use function str_replace;

final class PrivateApiAuthorizationMiddleware implements MiddlewareInterface
{
    private RolesRepository $rolesRepository;
    private array $config;

    public function __construct(RolesRepository $rolesRepository, array $config)
    {
        $this->rolesRepository = $rolesRepository;
        $this->config          = $config;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() === 'OPTIONS' || $request->getUri()->getPath() === '/_reset' || ($request->getMethod() === 'POST' && $request->getUri()->getPath() === '/sessions')) {
            return $handler->handle($request);
        }

        $authHeader = str_replace('Bearer ', '', $request->getHeaderLine('Authorization'));
        if (empty($authHeader)) {
            throw AuthenticationFailed::create('Authentication failed.');
        }

        try {
            $key           = InMemory::base64Encoded($this->config['jwt']['key']);
            $configuration = Configuration::forSymmetricSigner(
                new Sha256(),
                $key
            );
            $token         = $configuration->parser()->parse((string) $authHeader);
            $constraints   = [
                new IssuedBy($this->config['jwt']['issuer']),
                new IdentifiedBy($this->config['jwt']['identifier']),
                new StrictValidAt(new SystemClock(new \DateTimeZone('UTC')), new \DateInterval('PT12H'))
            ];
            if (! $configuration->validator()->validate($token, ...$constraints)) {
                throw AuthenticationFailed::create('Authentication failed. Invalid Token or token expired.');
            }

            $claims   = $token->claims();
            $userId   = $claims->get('userId');
            $roleName = $claims->get('role');

            $role = RoleBasedAccessControl::fromPermissions($roleName, $this->rolesRepository->getRolePermissionsByRoleNameForUser($userId));

            $request = $request
                ->withAttribute('loggedUserId', $userId);
            $request = $request
                ->withAttribute('role', $role);
        } catch (Throwable $e) {
            throw AuthenticationFailed::create('Authentication failed.' . $e->getMessage());
        }

        return $handler->handle($request);
    }
}
