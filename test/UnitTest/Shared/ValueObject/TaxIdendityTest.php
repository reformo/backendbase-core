<?php

declare(strict_types=1);

namespace UnitTest\Shared\ValueObject;

use BackendBase\Shared\ValueObject\CorporateTaxId;
use BackendBase\Shared\ValueObject\PrivateTaxId;
use BackendBase\Shared\ValueObject\TaxIdentity;
use Fixtures\GeneratedValues;
use PHPUnit\Framework\TestCase;

final class TaxIdendityTest extends TestCase
{
    /**
     * @test
     */
    public function shouldSuccessfullyInit() : void
    {
        $privateTaxId   = new PrivateTaxId(GeneratedValues::PRIVATE_TAX_ID_EXAMPLE);
        $corporateTaxId = new CorporateTaxId(GeneratedValues::CORPORATE_TAX_ID_VALID_EXAMPLE);

        $privateTaxIdentity = TaxIdentity::fromPrivateCompany($privateTaxId, 'Anville', '2000');
        $this->assertSame(TaxIdentity::TAXPAYER_PRIVATE, $privateTaxIdentity->taxPayerType());
        $this->assertSame('Anville', $privateTaxIdentity->taxAdministrationOfficeName());
        $this->assertSame('2000', $privateTaxIdentity->taxAdministrationOfficeCode());
        $this->assertSame($privateTaxId->taxId(), $privateTaxIdentity->taxIdNumber());
        $corporateTaxIdentity = TaxIdentity::fromCorporateCompany($corporateTaxId, 'Anville', '2000');
        $this->assertSame(TaxIdentity::TAXPAYER_CORPORATE, $corporateTaxIdentity->taxPayerType());
    }
}
