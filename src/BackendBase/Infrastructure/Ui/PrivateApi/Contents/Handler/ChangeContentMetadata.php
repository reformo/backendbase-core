<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Contents\Handler;

use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\Content;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\ContentRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use Selami\Stdlib\Arrays\PayloadSanitizer;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_key_exists;

class ChangeContentMetadata implements RequestHandlerInterface
{
    private ContentRepository $contentsRepository;
    private GenericRepository $genericRepository;

    public function __construct(
        ContentRepository $contentsRepository,
        GenericRepository $genericRepository
    ) {
        $this->contentsRepository = $contentsRepository;
        $this->genericRepository  = $genericRepository;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var Role
         */
        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Contents::CMS_EDIT) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to add new content');
        }

        $loggedUserId = $request->getAttribute('loggedUserId');
        $contentId    = $request->getAttribute('contentId');

        $allowHtml = [
            '$.body' => [
                'allowedTags' => 'a|href,class,style;img|src,class,style;ul;ol;li;p;h1;h2;h3;h4;h5;quote;b;strong;br;div|class',
                'urlPrefixes' => 'http;https',
            ],
            '$.serpDescription' => ['allowedTags' => 'b;strong;'],
        ];

        $payload = PayloadSanitizer::sanitize($request->getParsedBody(), $allowHtml);
        /**
         * @var $content Content
         */
        $content = $this->genericRepository->findGeneric(Content::class, $contentId);

        $expireAt = array_key_exists('expireAt', $payload) && $payload['expireAt'] !== null ? new CarbonImmutable($payload['publishAt']) : $content->expireAt();

        $content->setRobots($payload['robots'] ?? $content->robots());
        $content->setRedirectUrl($payload['redirectUrl'] ?? $content->redirectUrl());
        $content->setIsActive($payload['isActive'] ?? $content->isActive());
        $content->setTags($payload['tags'] ?? $content->tags());
        $content->setCoverImageLandscape(array_key_exists('coverImageLandscape', $payload) ? $payload['coverImageLandscape'] : $content->coverImageLandscape());
        $content->setCoverImagePortrait(array_key_exists('coverImagePortrait', $payload) ? $payload['coverImagePortrait'] : $content->coverImagePortrait());
        $content->setPublishAt(array_key_exists('publishAt', $payload) ? new CarbonImmutable($payload['publishAt']) : $content->publishAt());
        $content->setExpireAt($expireAt);
        $content->setSortOrder($payload['sortOrder'] ?? $content->sortOrder());
        $content->setUpdatedAt(new DateTimeImmutable());
        $content->setUpdatedBy($loggedUserId);
        $this->genericRepository->persistGeneric($content);

        return new EmptyResponse(204);
    }
}
