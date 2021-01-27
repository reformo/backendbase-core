<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Contents\Handler;

use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\Content;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\ContentRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use BackendBase\Shared\Services\PayloadSanitizer;
use DateTimeImmutable;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ChangeContentDetails implements RequestHandlerInterface
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
        $content = $this->genericRepository->findGeneric(Content::class, $contentId);
        $content->setTitle($payload['title'] ?? $content->title());
        $content->setImages($payload['images'] ?? []);
        $content->setSerpTitle($payload['serpTitle'] ?? $content->serpTitle());
        $content->setMetaDescription($payload['metaDescription'] ?? $content->metaDescription());
        $content->setSerpMetaDescription($payload['serpMetaDescription'] ?? $content->serpMetaDescription());
        $content->setKeywords($payload['keywords'] ?? $content->keywords());
        $content->setRobots($payload['robots'] ?? $content->robots());
        $content->setCanonical($payload['canonical'] ?? $content->canonical());
        $content->setMetadata($payload['metadata'] ?? $content->metadata());
        $content->setRedirect($payload['redirect'] ?? $content->redirect());
        $content->setBody($payload['body'] ?? $content->body());
        $content->setIsActive($payload['isActive'] ?? $content->isActive());
        $content->setUpdatedAt(new DateTimeImmutable());
        $content->setUpdatedBy($loggedUserId);
        $this->genericRepository->persistGeneric($content);

        return new EmptyResponse(204);
    }
}
