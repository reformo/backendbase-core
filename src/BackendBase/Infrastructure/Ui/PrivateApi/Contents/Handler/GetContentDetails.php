<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Contents\Handler;

use BackendBase\Domain\Collections\Interfaces\CollectionQuery;
use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\ContentRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function json_decode;

use const JSON_THROW_ON_ERROR;

class GetContentDetails implements RequestHandlerInterface
{
    private ContentRepository $contentsRepository;
    private GenericRepository $genericRepository;
    private CollectionQuery $collectionRepository;
    private array $config;

    public function __construct(
        ContentRepository $contentsRepository,
        GenericRepository $genericRepository,
        CollectionQuery $collectionRepository,
        array $config
    ) {
        $this->contentsRepository   = $contentsRepository;
        $this->genericRepository    = $genericRepository;
        $this->collectionRepository = $collectionRepository;
        $this->config               = $config;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var Role
         */
        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Contents::CMS_MENU) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to add new content');
        }

        $contentData        = $this->contentsRepository->getContentById($request->getAttribute('contentId'));
        $contentDetailsData = $this->contentsRepository->getContentDetailsById($request->getAttribute('contentId'));
        $categoryData       = $this->contentsRepository->getCategoryById($contentData['category']);
        $templateData       = $this->collectionRepository->findByKey($contentData['template']);

        return new JsonResponse([
            'content' => $contentData,
            'contentDetails' => $contentDetailsData,
            'category' => $categoryData,
            'bodyModel' => json_decode($templateData->metadata()['itemData']['_model'], true, 512, JSON_THROW_ON_ERROR),
            'validLanguages' => $this->config['i18n']['valid-languages-details'],
            'defaultLanguage' => $this->config['i18n']['default-language'],
        ], 200);
    }
}
