<?php

declare(strict_types=1);

namespace UnitTest\Shared\ValueObject;

use BackendBase\Shared\ValueObject\File;
use BackendBase\Shared\ValueObject\LocalizedDateTime;
use PHPUnit\Framework\TestCase;

final class FileTest extends TestCase
{
    /**
     * @test
     */
    public function shouldSuccessfullyInit() : void
    {
        $filePath = 'storage/temp/clover.xml';

        $file = new File($filePath, LocalizedDateTime::now());
        $this->assertSame('clover.xml', $file->getBasename());
        $this->assertSame('storage/temp', $file->getDirname());
        $this->assertSame('xml', $file->getExtension());
        $this->assertSame($filePath, $file->getFilePath());
        $this->assertSame('UTC', $file->getCreatedAt()->getTimezone()->getName());
    }
}
