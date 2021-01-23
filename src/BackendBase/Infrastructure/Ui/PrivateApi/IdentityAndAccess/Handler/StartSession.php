<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\IdentityAndAccess\Handler;

use BackendBase\Domain\IdentityAndAccess\Exception\LoginAttemptLimitExceeded;
use BackendBase\Domain\IdentityAndAccess\Model\Login;
use BackendBase\Domain\User\Exception\UserNotFound;
use BackendBase\Domain\User\Interfaces\UserRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\RolesRepository;
use BackendBase\Shared\ValueObject\Email;
use Laminas\Diactoros\Response\JsonResponse;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RateLimit\Exception\LimitExceeded;
use RateLimit\Rate;
use RateLimit\RedisRateLimiter;
use function hash;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Configuration;
use DateTimeImmutable;

class StartSession implements RequestHandlerInterface
{
    private UserRepository $userRepository;
    private RedisRateLimiter $redisRateLimiter;
    private RolesRepository $rolesRepository;
    private array $config;

    public function __construct(
        UserRepository $userRepository,
        RolesRepository $rolesRepository,
        RedisRateLimiter $redisRateLimiter,
        array $config
    ) {
        $this->userRepository   = $userRepository;
        $this->redisRateLimiter = $redisRateLimiter;
        $this->rolesRepository  = $rolesRepository;
        $this->config = $config;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $payload = $request->getParsedBody();
        try {
            $this->redisRateLimiter->limit(hash('sha256', $payload['email']), Rate::{Login::RATE_LIMIT_WINDOW}(Login::LOGIN_ATTEMPT_LIMIT));
        } catch (LimitExceeded $e) {
            throw LoginAttemptLimitExceeded::create(
                $e->getMessage() . '. '
                . 'You are allowed to attempt login '
                . Login::LOGIN_ATTEMPT_LIMIT . ' ' . Login::RATE_LIMIT_WINDOW_DESC . '.'
            );
        }
        $user = $this->userRepository
            ->getUserByEmail(Email::createFromString($payload['email']));

        if ($user->verifyPassword($payload['password']) === false) {
            throw UserNotFound::create('Invalid username and/or password');
        }

        $key = InMemory::base64Encoded($this->config['jwt']['key']);
        $configuration = Configuration::forSymmetricSigner(
            new Sha256(),
            $key
        );

        $now   = new DateTimeImmutable();
        $token = $configuration->builder()
            ->issuedBy($this->config['jwt']['issuer'])
            ->identifiedBy($this->config['jwt']['identifier'])
            ->issuedAt($now) // Configures the time that the token was issue (iat claim)
            ->canOnlyBeUsedAfter($now) // Configures the time that the token can be used (nbf claim)
            ->expiresAt($now->modify('+12 hours')) // Configures the expiration time of the token (exp claim)
            ->withClaim('userId', $user->id()->toString()) // Configures a new claim, called "uid"
            ->withClaim('role', $user->role()) // Configures a new claim, called "uid"
            ->getToken($configuration->signer(), $configuration->signingKey());

        $permissions = $this->rolesRepository->getRolePermissionsByRoleName($user->role());

        $data = [
            'accessToken' => $token->toString(),
            'createdAt' => $now->format(DATE_ATOM),
            'willExpireAt' => $now->modify('+12 hours')->format(DATE_ATOM),
            'userData' => [
                'firstName' => $user->firstName(),
                'lastName' => $user->lastName(),
                'email' => $user->email()->toString(),
                'role' => $user->role(),
                'permissions' => $permissions,
            ],
        ];

        return new JsonResponse($data, 201);
    }
}
