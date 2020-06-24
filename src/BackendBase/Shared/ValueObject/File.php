<?php

declare(strict_types=1);

namespace BackendBase\Shared\ValueObject;

use function pathinfo;

class File
{
    private array $pathInfo;
    private LocalizedDateTime $createdAt;

    public function __construct(string $filePath, LocalizedDateTime $createdAt)
    {
        $this->pathInfo = pathinfo($filePath);
        $this->createdAt = $createdAt;
    }

    public function getFilePath() : string
    {
        return $this->pathInfo['dirname'] . DIRECTORY_SEPARATOR . $this->pathInfo['basename'];
    }

    public function getDirname() : string
    {
        return $this->pathInfo['dirname'];
    }

    public function getBasename() : string
    {
        return $this->pathInfo['basename'];
    }

    public function getExtension() : string
    {
        return $this->pathInfo['extension'];
    }

    public function getCreatedAt(): LocalizedDateTime
    {
        return $this->createdAt;
    }



}
