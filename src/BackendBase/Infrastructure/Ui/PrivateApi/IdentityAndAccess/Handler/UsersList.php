<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\IdentityAndAccess\Handler;

use BackendBase\Domain\Administrators\Query\GetAllUsersPaginated;
use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Shared\CQRS\Interfaces\QueryBus;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Permissions\Rbac\Role;
use Mezzio\Helper\UrlHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function Psl\Dict\merge;
use function Psl\Math\ceil;

class UsersList implements RequestHandlerInterface
{
    public function __construct(
        private QueryBus $queryBus,
        private UrlHelper $serverUrlHelper
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var Role
         */
        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Users::USERS_MENU) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to list users');
        }

        $queryParams = $request->getQueryParams();
        $page        = (int) ($queryParams['page'] ?? 1);
        $pageSize    = (int) ($queryParams['pageSize'] ?? 5);
        $queryString = $queryParams['query'] ?? '';

        $data                    = $this->queryBus->handle(new GetAllUsersPaginated($queryString, $page, $pageSize));
        $lastPage                = ceil($data['_total'] / $data['_perPage']);
        $currentPage             = $data['_page'];
        $queryParamsForGenerator = [];
        $templated               = false;
        if (! empty($queryString)) {
            $templated               = true;
            $queryParamsForGenerator = ['query' => $queryString];
        }

        $data['_links'] = [
            'self' => [
                'href' => $this->serverUrlHelper->generate(
                    'users',
                    [],
                    merge($queryParamsForGenerator, ['page' => $currentPage])
                ),
                'templated' => $templated,
            ],
            'first' => [
                'href' => $this->serverUrlHelper->generate('users', [], merge($queryParamsForGenerator, ['page' => 1])),
                'templated' => $templated,
            ],
        ];
        if ($currentPage > 1) {
            $data['_links']['prev'] = [
                'href' => $this->serverUrlHelper->generate('users', [], merge($queryParamsForGenerator, ['page' => $currentPage - 1])),
                'templated' => $templated,
            ];
        }

        if ($currentPage < $lastPage) {
            $data['_links']['next'] = ['href' => $this->serverUrlHelper->generate('users', [], merge($queryParamsForGenerator, ['page' => $lastPage])), 'templated' => $templated];
        }

        $data['_links']['last']   = ['href' => $this->serverUrlHelper->generate('users', [], merge($queryParamsForGenerator, ['page' => $lastPage])), 'templated' => $templated];
        $data['_links']['search'] = ['href' => $this->serverUrlHelper->generate('users', []) . '?query={searchTerms}', 'templated' => true];
        $data['_embedded']        = [
            'user' => $data['users'],
            'role' => $data['roles'],
        ];
        unset($data['users'], $data['roles']);

        return new JsonResponse($data);
    }
}
