<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\IdentityAndAccess\Handler;

use BackendBase\Domain\Administrators\Exception\UserNotFound;
use BackendBase\Domain\Administrators\Query\AuthenticateUserWithEmail;
use BackendBase\Domain\IdentityAndAccess\Exception\LoginAttemptLimitExceeded;
use BackendBase\Domain\IdentityAndAccess\Model\Login;
use BackendBase\Shared\CQRS\Interfaces\QueryBus;
use DateTimeImmutable;
use Laminas\Diactoros\Response\JsonResponse;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RateLimit\Exception\LimitExceeded;
use RateLimit\Rate;
use RateLimit\RateLimiter;
use Selami\Stdlib\Arrays\PayloadSanitizer;

use function hash;
use function sprintf;

use const DATE_ATOM;

class StartSession implements RequestHandlerInterface
{
    private QueryBus $queryBus;
    private array $config;
    private RateLimiter $redisRateLimiter;

    public function __construct(
        QueryBus $queryBus,
        RateLimiter $redisRateLimiter,
        array $config
    ) {
        $this->queryBus         = $queryBus;
        $this->redisRateLimiter = $redisRateLimiter;
        $this->config           = $config;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        $payload    = PayloadSanitizer::sanitize($parsedBody);
        try {
            $this->redisRateLimiter->limit(hash('sha256', $payload['email']), Rate::{Login::RATE_LIMIT_WINDOW}(Login::LOGIN_ATTEMPT_LIMIT));
        } catch (LimitExceeded $e) {
            throw LoginAttemptLimitExceeded::create(
                $e->getMessage() . '. '
                . 'You are allowed to attempt login '
                . Login::LOGIN_ATTEMPT_LIMIT . ' ' . Login::RATE_LIMIT_WINDOW_DESC . '.'
            );
        }

        $message = new AuthenticateUserWithEmail($payload['email'], $parsedBody['password']);
        try {
            $user = $this->queryBus->handle($message);
        } catch (UserNotFound $exception) {
            throw UserNotFound::create(sprintf('Invalid username and/or password for %s:', $payload['email']));
        }

           $key        = InMemory::base64Encoded($this->config['jwt']['key']);
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
            ->withClaim('userId', $user->id()) // Configures a new claim, called "uid"
            ->withClaim('role', $user->role()) // Configures a new claim, called "uid"
            ->getToken($configuration->signer(), $configuration->signingKey());

        $data = [
            'accessToken' => $token->toString(),
            'createdAt' => $now->format(DATE_ATOM),
            'willExpireAt' => $now->modify('+12 hours')->format(DATE_ATOM),
            'userData' => $user,
        ];

        return new JsonResponse($data, 201);
    }
}
