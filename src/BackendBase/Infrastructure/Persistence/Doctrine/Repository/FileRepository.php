<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Repository;

use BackendBase\Infrastructure\Persistence\Doctrine\Entity\File;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\Order;
use DateTimeImmutable;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManager;
use Redislabs\Module\ReJSON\ReJSON;

class FileRepository
{
    protected EntityManager $entityManager;
    protected Connection $connection;

    private ReJSON $reJSON;

    public function __construct(EntityManager $entityManager, Connection $connection, ReJSON $reJSON)
    {
        $this->connection    = $connection;
        $this->entityManager = $entityManager;
        $this->reJSON        = $reJSON;
    }

    public function changeVersionStatus(string $version, int $isActive): void
    {
        $this->connection->executeQuery(
            "
            UPDATE public.files
               SET metadata = jsonb_set(metadata, '{isActive}', '" . $isActive . "')
            WHERE metadata->>'version' = :version
            ",
            ['version' => $version, 'isActive' => $isActive]
        );
    }

    public function findOrderFiles(string $orderId): array
    {
        $sql = "
        SELECT *
        FROM files f
        WHERE f.type = 'ORDER'
          AND f.metadata->>'orderId' = :orderId
          AND f.metadata->>'orderFileStatus' != '" . Order::DOSYA_STATUSU_ONAYLANMADI . "'
        ORDER BY f.uploaded_at ASC
       ";

        $statement = $this->connection->executeQuery($sql, ['orderId' => $orderId]);

        return $statement->fetchAll() ?? [];
    }

    private function convertArrayDataToDoctrineEntity(array $fileData): File
    {
        $doctrineFileEntity = new File();
        $doctrineFileEntity->setId($fileData['id']);
        $doctrineFileEntity->setFilePath($fileData['filePath']);
        $doctrineFileEntity->setType($fileData['type']);
        $doctrineFileEntity->setMetadata($fileData['metadata']);
        $doctrineFileEntity->setUploadedAt(new DateTimeImmutable());

        return $doctrineFileEntity;
    }

    public function addNewFile(array $fileData): void
    {
        $doctrinefileEntity = $this->convertArrayDataToDoctrineEntity($fileData);
        $this->entityManager->persist($doctrinefileEntity);
        $this->entityManager->flush();
    }

    public function getFileById(string $fileId): File
    {
        return $this->entityManager->find(File::class, $fileId);
    }

    public function update(File $file): void
    {
        $this->entityManager->persist($file);
        $this->entityManager->flush();
    }
}
