<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Contents\Handler;

use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\ContentRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_key_exists;

class GetContentListByCategory implements RequestHandlerInterface
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
        if ($role->hasPermission(Permissions\Contents::CMS_MENU) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to add new content');
        }

        $language                      = $this->config['i18n']['default-language'];
        $region                        = $this->config['i18n']['default-region'];
        $category                      = $request->getAttribute('category');
        $categoryData                  = $this->contentsRepository->getCategory($category);
        $contentsData                  = [];
        $categoryData['subCategories'] = null;
        if (array_key_exists('useSubItems', $categoryData['metadata']) && $categoryData['metadata']['useSubItems'] === true) {
            $categoryData['subCategories'] = $this->contentsRepository->getCategoriesByParentId($categoryData['id']);
        }

        if ($categoryData['subCategories'] === null) {
            $contentsData = $this->contentsRepository->getContentsByCategory($categoryData['id'], $language, $region);
        }

        return new JsonResponse([
            'category' => $categoryData,
            'contents' => $contentsData,
        ], 200);
    }
}
