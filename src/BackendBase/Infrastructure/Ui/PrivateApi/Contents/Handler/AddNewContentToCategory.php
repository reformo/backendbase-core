<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Contents\Handler;

use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\Content;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\ContentDetail;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\ContentRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use Selami\Stdlib\Arrays\PayloadSanitizer;
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
    private array $config;

    public function __construct(
        ContentRepository $contentsRepository,
        GenericRepository $genericRepository,
        array $config
    ) {
        $this->contentsRepository = $contentsRepository;
        $this->genericRepository  = $genericRepository;
        $this->config             = $config;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
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
        $payload      = PayloadSanitizer::sanitize($request->getParsedBody());
        $categoryData = $this->contentsRepository->getCategoryById($request->getAttribute('category'));
        $slug         = '/' . $categoryData['slug'] . '/' . $slugify->slugify($payload['title']);

        $content = new Content();
        $content->setId(Uuid::uuid4()->toString());
        $content->setCategory($request->getAttribute('category'));
        $content->setTags([]);
        $content->setTemplate($payload['template']);
        $content->setIsActive(Content::CONTENT_IS_PASSIVE);
        $content->setIsDeleted(Content::CONTENT_IS_ACCESSIBLE);
        $content->setRobots('');
        $content->setPublishAt(new DateTimeImmutable());
        $content->setSortOrder(Content::generateSortValue());
        $content->setCreatedAt(new DateTimeImmutable());
        $content->setUpdatedAt(new DateTimeImmutable());
        $content->setCreatedBy($loggedUserId);
        $content->setUpdatedBy($loggedUserId);

        $this->genericRepository->addQueueToPersist($content);

        foreach ($this->config['i18n']['valid-languages'] as $language) {
            $contentDetail = new ContentDetail();
            $contentDetail->setId(Uuid::uuid4()->toString());
            $contentDetail->setContentId($content->id());
            $contentDetail->setLanguage($language);
            $contentDetail->setRegion($language);
            $contentDetail->setTitle($payload['title']);
            $contentDetail->setSlug($slug);
            $contentDetail->setBody([]);
            $contentDetail->setBodyFulltext('');
            $contentDetail->setSerpTitle($payload['title']);
            $contentDetail->setDescription('');
            $contentDetail->setKeywords('');
            $contentDetail->setIsActive(Content::CONTENT_IS_ACTIVE);
            $this->genericRepository->addQueueToPersist($contentDetail);
        }

        $this->genericRepository->flush();

        return new EmptyResponse(204, ['Backendbase-Insert-Id' => $content->id()]);
    }
}
