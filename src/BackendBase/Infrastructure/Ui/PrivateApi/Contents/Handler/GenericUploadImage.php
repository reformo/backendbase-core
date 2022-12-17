<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Contents\Handler;

use BackendBase\Infrastructure\Persistence\Doctrine\Repository\FileRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use BackendBase\Shared\Services\MessageBus\Interfaces\CommandBus;
use Cocur\Slugify\Slugify;
use Intervention\Image\ImageManagerStatic as Image;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Permissions\Rbac\Role;
use League\Flysystem\Filesystem;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

use function array_key_exists;
use function basename;
use function str_replace;

class GenericUploadImage implements RequestHandlerInterface
{
    private const RESULT_ROWS_LIMIT = 25;
    private const ULID_LOWERCASE    = true;
    private CommandBus $commandBus;
    private Slugify $slugifier;

    public function __construct(
        CommandBus $commandBus,
        private FileRepository $fileRepository,
        private GenericRepository $genericRepository,
        private Filesystem $fileSystem,
        private array $config
    ) {
        $this->commandBus        = $commandBus;
        $this->slugifier         = new Slugify(['rulesets' => ['default', 'turkish']]);
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

        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Contents::CMS_EDIT) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to upload an image for contents');
        }     */

        $loggedUserId      = $request->getAttribute('loggedUserId');
        $queryParams       = $request->getQueryParams();
        $aspectRatioWidth  =  array_key_exists('arWidth', $queryParams) ? (int) $queryParams['arWidth'] : null;
        $aspectRatioHeight = array_key_exists('arHeight', $queryParams)  ? (int) $queryParams['arHeight'] : 500;
        $key               = 'images';
        $type              = $queryParams['type'] ?? 'CONTENTS';

        $uploadedFile = $request->getAttribute('uploadedFilePath');
        $module       = $request->getAttribute('moduleName');
        $fileId       = Uuid::uuid4()->toString();
        $filePath     =  'app/images/' . $module . '/' . $fileId . '.' . self::findExtension($this->fileSystem->getMimetype($uploadedFile));

        $this->fileSystem->rename($uploadedFile, $filePath);
        $img = Image::make('data/storage/' . $filePath);
        $img->resize($aspectRatioWidth, $aspectRatioHeight, static function ($constraint): void {
            $constraint->aspectRatio();
        });
        $img->save('data/storage/' . $filePath);
        $fileData = [
            'id' => $fileId ,
            'filePath' => $filePath,
            'type' => $type,
            'metadata' => [
                'auditLog' => ['userId' => $loggedUserId],
                'module' => $module,
                'fileName' => $queryParams['fileName'] ?? basename($filePath),
            ],
        ];
        $this->fileRepository->addNewFile($fileData);

        return new JsonResponse(['image' => str_replace('app/', '/', $filePath)], 201);
    }
}
