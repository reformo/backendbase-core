<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Contents\Handler;

use BackendBase\Domain\Collections\Interfaces\CollectionRepository;
use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\FileRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use BackendBase\Shared\Services\MessageBus\Interfaces\CommandBus;
use BackendBase\Shared\Services\WebpConverter;
use Cocur\Slugify\Slugify;
use Gumlet\ImageResize;
use ImageOptimizer\OptimizerFactory;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Permissions\Rbac\Role;
use League\Flysystem\Filesystem;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

use function basename;
use function hrtime;
use function pathinfo;
use function str_ireplace;
use function str_replace;

use const PATHINFO_FILENAME;

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

    private static function findExtension(string $mimetype): string
    {
        return [
            'image/jpeg' => 'jpg',
            'image/pipeg' => 'jfif',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ][$mimetype];
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var Role
         */
        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Contents::CMS_EDIT) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to upload an image for contents');
        }

        $loggedUserId = $request->getAttribute('loggedUserId');
        $queryParams  = $request->getQueryParams();
        $type         = $queryParams['type'] ?? 'CONTENTS';

        $uploadedFile = $request->getAttribute('uploadedFilePath');
        $contentId    = $request->getAttribute('contentId');
        $extension    = self::findExtension($this->fileSystem->getMimetype($uploadedFile));
        $fileId       = Uuid::uuid4()->toString();

        $fileName = empty($queryParams['fileName']) ? $fileId . '.' . $extension : $queryParams['fileName'];
        $fileName = pathinfo($fileName, PATHINFO_FILENAME);

        $filePath =  'app/images/content/' . $contentId . '/' . $this->slugifier->slugify($fileName) . '-' . hrtime(true) . '.' . $extension;

        $this->fileSystem->rename($uploadedFile, $filePath);
        $mobileFile = str_ireplace('.' . $extension, '-mobile.' . $extension, 'data/storage/' . $filePath);
        $image      = new ImageResize('data/storage/' . $filePath);
        if ($image->getSourceWidth() > 992) {
            $image->resizeToWidth(992, false);
        }

        $image->save($mobileFile);

        WebpConverter::convert('data/storage/' . $filePath);
        WebpConverter::convert($mobileFile);

        $factory   = new OptimizerFactory(['ignore_errors' => false]);
        $optimizer = $factory->get();

        $optimizer->optimize('data/storage/' . $filePath);

        $optimizer->optimize($mobileFile);

        $fileData = [
            'id' => $fileId ,
            'filePath' => $filePath,
            'type' => $type,
            'metadata' => [
                'auditLog' => ['userId' => $loggedUserId],
                'contentId' => $contentId,
                'fileName' => $queryParams['fileName'] ?? basename($filePath),
            ],
        ];
        $this->fileRepository->addNewFile($fileData);

        return new JsonResponse([
            'image' => str_replace('app/', '/', $filePath),
            'uploaded' => 1,
            'fileName' => basename($filePath),
            'url' => $this->config['app']['cdn-url'] . str_replace('app/', '/', $filePath),
        ], 201);
    }
}
