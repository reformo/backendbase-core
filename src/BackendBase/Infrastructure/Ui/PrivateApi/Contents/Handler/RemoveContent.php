<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Contents\Handler;

use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\Content;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\ContentRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use DateTimeImmutable;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RemoveContent implements RequestHandlerInterface
{
    public function __construct(private ContentRepository $contentsRepository, private GenericRepository $genericRepository)
    {
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
        $content      = $this->genericRepository->findGeneric(Content::class, $contentId);
        $content->setIsDeleted(Content::CONTENT_IS_NOT_ACCESSIBLE);
        $content->setUpdatedAt(new DateTimeImmutable());
        $content->setUpdatedBy($loggedUserId);
        $this->genericRepository->persistGeneric($content);

        return new EmptyResponse(204);
    }
}
