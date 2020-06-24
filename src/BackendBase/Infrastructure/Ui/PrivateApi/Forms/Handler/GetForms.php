<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Forms\Handler;

use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\Forms;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\ContentRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetForms implements RequestHandlerInterface
{
    private GenericRepository $genericRepository;

    public function __construct(
        GenericRepository $genericRepository
    ) {
        $this->genericRepository  = $genericRepository;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /**
         * @var Role
         */
        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Forms::FORMS_MENU) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to list forms');
        }
        $forms = $this->genericRepository->getList(Forms::class, ['is_active' => 1], 'created_at DESC');

        return new JsonResponse(['forms' => $forms, 'total' => count($forms)], 200);
    }
}
