<?php

declare(strict_types=1);

namespace UnitTest\Shared\ValueObject;

use BackendBase\Shared\ValueObject\Exception\InvalidPrivateTaxIdNumber;
use BackendBase\Shared\ValueObject\PrivateTaxId;
use Fixtures\GeneratedValues;
use PHPUnit\Framework\TestCase;

final class PrivateTaxIdTest extends TestCase
{
    /**
     * @test
     */
    public function shouldSuccessfullyInit(): void
    {
        $privateTaxId = new PrivateTaxId(GeneratedValues::PRIVATE_TAX_ID_EXAMPLE);
        $this->assertSame(GeneratedValues::PRIVATE_TAX_ID_EXAMPLE, $privateTaxId->taxId());
    }

    /**
     * @test
     */
    public function shouldFailForTaxIdStartsWithZero(): void
    {
        $this->expectException(InvalidPrivateTaxIdNumber::class);
        $this->expectExceptionMessage('Private tax id cannot be started with zero(0): 01234567890');
        new PrivateTaxId('01234567890');
    }

    /**
     * @test
     */
    public function shouldFailForInvalidLengthTaxId(): void
    {
        $this->expectException(InvalidPrivateTaxIdNumber::class);
        $this->expectExceptionMessage('Private tax id length must be 11: 1234567891');
        new PrivateTaxId('1234567891');
    }

    /**
     * @test
     */
    public function shouldFailForInvalidChecksum2(): void
    {
        $this->expectException(InvalidPrivateTaxIdNumber::class);
        $this->expectExceptionMessage(
            'Private tax id checksum for 10th number failed: '
            . GeneratedValues::PRIVATE_TAX_ID_EXAMPLE_FOR_CHECKSUM_10_FAILURE
        );
        new PrivateTaxId(GeneratedValues::PRIVATE_TAX_ID_EXAMPLE_FOR_CHECKSUM_10_FAILURE);
    }

    /**
     * @test
     */
    public function shouldFailForInvalidChecksum1(): void
    {
        $this->expectException(InvalidPrivateTaxIdNumber::class);
        $this->expectExceptionMessage(
            'Private tax id checksum for 11th number failed: '
            . GeneratedValues::PRIVATE_TAX_ID_EXAMPLE_FOR_CHECKSUM_11_FAILURE
        );
        new PrivateTaxId(GeneratedValues::PRIVATE_TAX_ID_EXAMPLE_FOR_CHECKSUM_11_FAILURE);
    }
}
