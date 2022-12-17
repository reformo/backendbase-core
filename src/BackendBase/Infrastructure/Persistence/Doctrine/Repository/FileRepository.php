<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Repository;

use BackendBase\Infrastructure\Persistence\Doctrine\Entity\File;
use DateTimeImmutable;

class FileRepository extends GenericRepository
{
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

    public function update(File $file, string $entityId, array $entityData): void
    {
        $this->entityManager->persist($file);
        $this->entityManager->flush();
    }
}
