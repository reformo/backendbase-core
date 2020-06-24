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

class GetCategoryList implements RequestHandlerInterface
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
        if ($role->hasPermission(Permissions\Contents::CMS_MENU) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to list categories');
        }
        $categories = $this->contentsRepository->getCategories('lt:cms:categories');

        return new JsonResponse(['categories' => $categories], 200);
    }
}
