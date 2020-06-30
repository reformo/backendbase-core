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
        $this->assertSame('clover.xml', $file->basename());
        $this->assertSame('storage/temp', $file->dirname());
        $this->assertSame('xml', $file->extension());
        $this->assertSame($filePath, $file->filePath());
        $this->assertSame('UTC', $file->createdAt()->getTimezone()->getName());
    }
}
