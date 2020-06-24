<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\IdentityAndAccess\Handler;

use BackendBase\Domain\IdentityAndAccess\Exception\LoginAttemptLimitExceeded;
use BackendBase\Domain\IdentityAndAccess\Model\Login;
use BackendBase\Domain\User\Exception\UserNotFound;
use BackendBase\Domain\User\Interfaces\UserRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\RolesRepository;
use Laminas\Diactoros\Response\JsonResponse;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RateLimit\Exception\LimitExceeded;
use RateLimit\Rate;
use RateLimit\RedisRateLimiter;
use BackendBase\Shared\ValueObject\Email;
use function hash;
use function time;

class StartSession implements RequestHandlerInterface
{
    private UserRepository $userRepository;
    private RedisRateLimiter $redisRateLimiter;
    private RolesRepository $rolesRepository;

    public function __construct(
        UserRepository $userRepository,
        RolesRepository $rolesRepository,
        RedisRateLimiter $redisRateLimiter
    ) {
        $this->userRepository   = $userRepository;
        $this->redisRateLimiter = $redisRateLimiter;
        $this->rolesRepository  = $rolesRepository;
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

        $signer = new Sha256();
        $time   = time();
        $token  = (new Builder())->issuedBy('storage') // Configures the issuer (iss claim)
        //    ->permittedFor('http://example.org') // Configures the audience (aud claim)
        //    ->identifiedBy('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
        ->issuedAt($time) // Configures the time that the token was issue (iat claim)
        ->canOnlyBeUsedAfter($time) // Configures the time that the token can be used (nbf claim)
        ->expiresAt($time + 60*60*12) // Configures the expiration time of the token (exp claim)
        ->withClaim('userId', $user->id()->toString()) // Configures a new claim, called "uid"
        ->withClaim('role', $user->role()) // Configures a new claim, called "uid"
        ->getToken($signer, new Key('d81c8751fdd0a01e62b7acac5bea23a0d7d29beb03e428b863d02376aea628c1'));

        $permissions = $this->rolesRepository->getRolePermissionsByRoleName($user->role());

        $data = [
            'accessToken' => (string) $token,
            'createdAt' => $time,
            'willExpireAt' => $time + 3600,
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
