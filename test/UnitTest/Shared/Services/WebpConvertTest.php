<?php

declare(strict_types=1);

namespace UnitTest\Shared\Services;

use BackendBase\Shared\Services\WebpConverter;
use PHPUnit\Framework\TestCase;

use function str_replace;

/**
 * Image by PIRO4D from Pixabay
 *
 * @link https://pixabay.com/users/piro4d-2707530/?utm_source=link-attribution&amp;utm_medium=referral&amp;utm_campaign=image&amp;utm_content=2126896
 * @link https://pixabay.com/?utm_source=link-attribution&amp;utm_medium=referral&amp;utm_campaign=image&amp;utm_content=2126896
 */
final class WebpConvertTest extends TestCase
{
    /**
     * @test
     */
    public function shouldConvertJpegToWebpSuccessfully(): void
    {
        $filename    = 'test/Fixtures/planetary-gear-2126896_1280.jpg';
        $newFileName = WebpConverter::convert($filename);
        $this->assertSame($filename . '.webp', $newFileName);
        $newFileName = WebpConverter::convert($filename, 80, 'replace');
        $this->assertSame(str_replace('.jpg', '.webp', $filename), $newFileName);
    }
}
