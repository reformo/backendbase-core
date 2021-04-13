<?php

declare(strict_types=1);

namespace UnitTest\Shared\ValueObject;

use BackendBase\Shared\ValueObject\Exception\InvalidDateTimeProvided;
use BackendBase\Shared\ValueObject\LocalizedDateTime;
use DateInterval;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

final class LocalizedDatetimeTest extends TestCase
{
    /**
     * @test
     */
    public function shouldSuccessfullyInit(): void
    {
        $datetime      = '2017-11-06 23:47:00';
        $timezone      = 'Europe/Istanbul';
        $localizedDate = LocalizedDateTime::fromString($datetime, $timezone);
        $this->assertSame($timezone, $localizedDate->getTimezone()->getName());
        $this->assertSame($datetime, $localizedDate->toFormat('Y-m-d H:i:s'));

        $addedDate = $localizedDate->withAddedInterval(new DateInterval('P1D'));
        $this->assertSame('2017-11-07 23:47:00', $addedDate->toFormat('Y-m-d H:i:s'));
        $this->assertSame('1', $localizedDate->diff($addedDate)->format('%a'));

        $subtractedDate = $localizedDate->withSubtractedInterval(new DateInterval('P1D'));
        $this->assertSame('2017-11-05 23:47:00', $subtractedDate->toFormat('Y-m-d H:i:s'));

        $newTimeZone = $localizedDate->withNewTimezone(new DateTimeZone('UTC'));
        $this->assertSame('UTC', $newTimeZone->getTimezone()->getName());
        $this->assertNotSame($datetime, $newTimeZone->toFormat('Y-m-d H:i:s'));

        $now = LocalizedDateTime::now('UTC');
        $this->assertSame('UTC', $now->getTimezone()->getName());
    }

    /**
     * @test
     */
    public function shouldFailInvalidDatetimeFormat(): void
    {
        $this->expectException(InvalidDateTimeProvided::class);
        LocalizedDateTime::fromString('1991-09-24 00:00');
    }
}
