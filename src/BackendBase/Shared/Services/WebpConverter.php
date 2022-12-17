<?php

declare(strict_types=1);

namespace BackendBase\Shared\Services;

use InvalidArgumentException;

use function file_exists;
use function imagecreatefromjpeg;
use function imagecreatefrompng;
use function imagedestroy;
use function imagewebp;
use function mime_content_type;
use function pathinfo;
use function str_replace;

use const PATHINFO_EXTENSION;

class WebpConverter
{
    private function __construct(private string $filename, private int $quality, private string $extensionType)
    {
    }

    public static function convert(string $filename, int $quality = 85, string $extensionType = 'append'): string
    {
        $converter = new self($filename, $quality, $extensionType);

        return $converter->convertToWebp();
    }

    private function convertToWebp(): string
    {
        if (! file_exists($this->filename)) {
            throw new InvalidArgumentException('File does not exist');
        }

        $fileMimeType  = mime_content_type($this->filename);
        $newFilename   = $this->getFileName();
        $imageResource = $this->getImageResource($fileMimeType);
        imagewebp($imageResource, $newFilename, $this->quality);
        imagedestroy($imageResource);

        return $newFilename;
    }

    private function getFileName(): string
    {
        if ($this->extensionType === 'replace') {
            $extension = pathinfo($this->filename, PATHINFO_EXTENSION);

            return str_replace('.' . $extension, '.webp', $this->filename);
        }

        return $this->filename . '.webp';
    }

    private function getImageResource(string $mimeType)
    {
        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($this->filename);

                break;
            case 'image/png':
                return imagecreatefrompng($this->filename);

                break;
        }
    }
}
