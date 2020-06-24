<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Forms\Handler;

use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\FormData;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\Forms;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\ContentRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetFormsData implements RequestHandlerInterface
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
        $formData = [];

        $limit = 25;
        $queryParams = $request->getQueryParams();
        $page = $queryParams['page'] ?? 1;
        $total = $this->genericRepository->getListTotal(FormData::class, ['form_id'=> $request->getAttribute('formId')]);
        $pageCount = ceil($total/$limit);
        if ($page > $pageCount) {
            $page = $pageCount;
        }
        if ($page <1) {
            $page = 1;
        }
        $offset = $limit * ($page-1);
        $pagination = [
            'offset' => $offset,
            'limit' => $limit
        ];
        if ($total > 0) {
            $formData = $this->genericRepository->getList(
                FormData::class,
                ['form_id' => $request->getAttribute('formId')],
                "created_at DESC",
                $pagination
            );
        }
        return new JsonResponse([
            'formData' => $formData,
            'total'=> $total
        ], 200);
    }
}
