<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Contents\Handler;

use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\Content;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\QuotationRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\VehicleRepository;
use BackendBase\Shared\Services\MessageBus\Interfaces\QueryBus;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_values;

class RemoveImage implements RequestHandlerInterface
{
    private QueryBus $queryBus;
    private VehicleRepository $vehicleRepository;
    private QuotationRepository $quotationRepository;

    public function __construct(
        QueryBus $queryBus,
        VehicleRepository $vehicleRepository,
        QuotationRepository $quotationRepository,
        private GenericRepository $genericRepository,
        private array $config
    ) {
        $this->queryBus          = $queryBus;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var Role
         */
        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Contents::CMS_EDIT) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to edit content');
        }

        $index = (int) $request->getAttribute('index');

        $contentId = $request->getAttribute('contentId');
        $content   = $this->genericRepository->findGeneric(Content::class, $contentId);

        $images = $content->images();
        unset($images[$index]);

        $content->setImages(array_values($images));

        $this->genericRepository->persistGeneric($content);

        return new EmptyResponse(204);
    }
}
