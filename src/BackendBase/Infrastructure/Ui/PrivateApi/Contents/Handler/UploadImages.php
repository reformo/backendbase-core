<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Contents\Handler;

use Cocur\Slugify\Slugify;
use BackendBase\Domain\Collections\Interfaces\CollectionRepository;
use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\Content;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\FileRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use Intervention\Image\ImageManagerStatic as Image;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Permissions\Rbac\Role;
use League\Flysystem\Filesystem;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;
use BackendBase\Shared\Services\MessageBus\Interfaces\CommandBus;
use function apcu_delete;
use function array_key_exists;
use function basename;
use function str_replace;

class UploadImages implements RequestHandlerInterface
{
    private const RESULT_ROWS_LIMIT = 25;
    private const ULID_LOWERCASE    = true;

    private array $config;
    private Filesystem $fileSystem;
    private FileRepository $fileRepository;
    private CommandBus $commandBus;
    private CollectionRepository $collectionRepository;
    private GenericRepository $genericRepository;
    private Slugify $slugifier;

    public function __construct(
        CommandBus $commandBus,
        FileRepository $fileRepository,
        CollectionRepository $collectionRepository,
        GenericRepository $genericRepository,
        Filesystem $fileSystem,
        array $config
    ) {
        $this->config               = $config;
        $this->commandBus           = $commandBus;
        $this->fileSystem           = $fileSystem;
        $this->fileRepository       = $fileRepository;
        $this->genericRepository    = $genericRepository;
        $this->collectionRepository = $collectionRepository;
        $this->slugifier            = new Slugify(['rulesets' => ['default', 'turkish']]);
    }

    private static function findExtension(string $mimetype) : string
    {
        return [
            'image/jpeg' => 'jpg',
            'image/pipeg' => 'jfif',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ][$mimetype];
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /**
         * @var Role
         */
        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Contents::CMS_EDIT) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to upload an image for contents');
        }
        $loggedUserId      = $request->getAttribute('loggedUserId');
        $queryParams       = $request->getQueryParams();
        $aspectRatioWidth  =  array_key_exists('arWidth', $queryParams) ? (int) $queryParams['arWidth'] : null;
        $aspectRatioHeight = array_key_exists('arHeight', $queryParams)  ? (int) $queryParams['arHeight'] : 500;
        $key               = 'images';
        $type              = $queryParams['type'] ?? 'CONTENTS';

        $uploadedFile = $request->getAttribute('uploadedFilePath');
        $contentId    = $request->getAttribute('contentId');
        $fileId       = Uuid::uuid4()->toString();
        $filePath     =  'app/images/content/' . $contentId . '/' . $fileId . '.' . self::findExtension($this->fileSystem->getMimetype($uploadedFile));

        $this->fileSystem->rename($uploadedFile, $filePath);
        $img = Image::make('data/storage/' . $filePath);
        $img->resize($aspectRatioWidth, $aspectRatioHeight, static function ($constraint) : void {
            $constraint->aspectRatio();
        });
        $img->save('data/storage/' . $filePath);
        $fileData = [
            'id' =>$fileId ,
            'filePath' => $filePath,
            'type' => $type,
            'metadata' => [
                'auditLog' => ['userId' => $loggedUserId],
                'contentId' => $contentId,
                'fileName' => $queryParams['fileName'] ?? basename($filePath),
            ],
        ];
        $this->fileRepository->addNewFile($fileData);
        return new JsonResponse(['image' => str_replace('app/', '/', $filePath) ], 201);
    }
}
