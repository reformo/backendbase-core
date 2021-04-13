<?php

declare(strict_types=1);

namespace UnitTest\Shared\ValueObject;

use BackendBase\Shared\ValueObject\CorporateTaxId;
use BackendBase\Shared\ValueObject\Exception\InvalidCorporateTaxIdNumber;
use Fixtures\GeneratedValues;
use PHPUnit\Framework\TestCase;

final class CorporateTaxIdTest extends TestCase
{
    /**
     * @test
     */
    public function shouldSuccessfullyInit(): void
    {
        $corporateTaxId = new CorporateTaxId(GeneratedValues::CORPORATE_TAX_ID_VALID_EXAMPLE);
        $this->assertSame(GeneratedValues::CORPORATE_TAX_ID_VALID_EXAMPLE, $corporateTaxId->taxId());
    }

    /**
     * @test
     */
    public function shouldFailForInvalidLengthTaxId(): void
    {
        $this->expectException(InvalidCorporateTaxIdNumber::class);
        $this->expectExceptionMessage(
            'Corporate tax id length must be 10: ' . GeneratedValues::CORPORATE_TAX_ID_INVALID_LENGTH_EXAMPLE
        );
        new CorporateTaxId(GeneratedValues::CORPORATE_TAX_ID_INVALID_LENGTH_EXAMPLE);
    }

    /**
     * @test
     */
    public function shouldFailForInvalidChecksum(): void
    {
        $this->expectException(InvalidCorporateTaxIdNumber::class);
        $this->expectExceptionMessage(
            'Corporate tax id checksum for 10th number failed: '
            . GeneratedValues::CORPORATE_TAX_ID_INVALID_CHECKSUM_EXAMPLE
        );
        new CorporateTaxId(GeneratedValues::CORPORATE_TAX_ID_INVALID_CHECKSUM_EXAMPLE);
    }
}
