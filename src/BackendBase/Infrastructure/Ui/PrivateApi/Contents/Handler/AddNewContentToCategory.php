<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Contents\Handler;

use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\Content;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\ContentRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use BackendBase\Shared\Services\PayloadSanitizer;
use Cocur\Slugify\Slugify;
use DateTimeImmutable;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

class AddNewContentToCategory implements RequestHandlerInterface
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

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /**
         * @var Role
         */
        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Contents::CMS_CREATE) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to add new content');
        }
        $slugify      = new Slugify(['rulesets' => ['default', 'turkish']]);
        $loggedUserId = $request->getAttribute('loggedUserId');
        $allowHtml    = [
            '$.body' => [
                'allowedTags'=>'a|href,class,style;img|src,class,style;ul;ol;li;p;h1;h2;h3;h4;h5;quote;b;strong;br',
                'urlPrefixes' => 'http;https',
            ],
            '$.serpDescription' => ['allowedTags' => 'b;strong;'],
        ];

        $payload          = PayloadSanitizer::sanitize($request->getParsedBody(), $allowHtml);
        $metadata         = $payload['metadata'] ?? [];
        $categoryData     = $this->contentsRepository->getCategory($request->getAttribute('category'));
        $metadata['slug'] = $metadata['slug'] ?? ('/' . $categoryData['slug'] . '/' . $slugify->slugify($payload['title']));

        $content = new Content();
        $content->setId($payload['tenantId'] ?? Uuid::uuid4()->toString());
        $content->setType('full');
        $content->setCategory($request->getAttribute('category'));
        $content->setTitle($payload['title']);
        $content->setSerpTitle($payload['serpTitle'] ?? $payload['title']);
        $content->setMetaDescription($payload['metaDescription'] ?? null);
        $content->setSerpMetaDescription($payload['serpMetaDescription'] ?? null);
        $content->setKeywords($payload['keywords'] ?? null);
        $content->setRobots($payload['robots'] ?? null);
        $content->setCanonical($payload['canonical'] ?? null);
        $content->setMetadata($metadata);
        $content->setRedirect($payload['redirect'] ?? null);
        $content->setBody($payload['body'] ?? '');
        $content->setIsActive(Content::CONTENT_IS_ACTIVE);
        $content->setIsDeleted(Content::CONTENT_IS_ACCESSIBLE);
        $content->setSortOrder(Content::generateSortValue());
        $content->setCreatedAt(new DateTimeImmutable());
        $content->setUpdatedAt(new DateTimeImmutable());
        $content->setCreatedBy($loggedUserId);
        $content->setUpdatedBy($loggedUserId);
        $this->genericRepository->persistGeneric($content);

        return new EmptyResponse(204, ['storage-Insert-Id' => $content->id()]);
    }
}
