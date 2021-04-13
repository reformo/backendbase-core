<?php

declare(strict_types=1);

namespace UnitTest\Shared\ValueObject;

use BackendBase\Shared\ValueObject\CompanyInfo;
use BackendBase\Shared\ValueObject\CorporateTaxId;
use BackendBase\Shared\ValueObject\Exception\InvalidCompanyLegalName;
use BackendBase\Shared\ValueObject\Exception\InvalidCompanyShortName;
use BackendBase\Shared\ValueObject\Exception\InvalidCompanyType;
use BackendBase\Shared\ValueObject\TaxIdentity;
use Fixtures\GeneratedValues;
use PHPUnit\Framework\TestCase;

final class CompanyInfoTest extends TestCase
{
    /**
     * @test
     */
    public function shouldSuccessfullyInit(): void
    {
        $taxIdentity = TaxIdentity::fromCorporateCompany(
            new CorporateTaxId(GeneratedValues::CORPORATE_TAX_ID_VALID_EXAMPLE),
            'Maltepe',
            '2000'
        );
        $companyInfo = new CompanyInfo(
            $taxIdentity,
            'Lorax Inc',
            'Lorax',
            CompanyInfo::COMPANY_TYPE_CORPORATION
        );
        $this->assertSame(
            GeneratedValues::CORPORATE_TAX_ID_VALID_EXAMPLE,
            $companyInfo->taxIdentity()->taxIdNumber()
        );
        $this->assertSame(CompanyInfo::COMPANY_TYPE_CORPORATION, $companyInfo->companyType());
        $this->assertSame('Lorax Inc', $companyInfo->legalName());
        $this->assertSame('Lorax', $companyInfo->shortName());
    }

    /**
     * @test
     */
    public function shouldFailForInvalidLegalName(): void
    {
        $taxIdentity = TaxIdentity::fromCorporateCompany(
            new CorporateTaxId(GeneratedValues::CORPORATE_TAX_ID_VALID_EXAMPLE),
            'Anville',
            '2000'
        );
        $this->expectException(InvalidCompanyLegalName::class);
        new CompanyInfo(
            $taxIdentity,
            'Lor',
            'Lorax',
            CompanyInfo::COMPANY_TYPE_CORPORATION
        );
    }

    /**
     * @test
     */
    public function shouldFailForInvalidShortName(): void
    {
        $taxIdentity = TaxIdentity::fromCorporateCompany(
            new CorporateTaxId(GeneratedValues::CORPORATE_TAX_ID_VALID_EXAMPLE),
            'Anville',
            '2000'
        );
        $this->expectException(InvalidCompanyShortName::class);
        new CompanyInfo(
            $taxIdentity,
            'Lorax Inc',
            '',
            CompanyInfo::COMPANY_TYPE_CORPORATION
        );
    }

    /**
     * @test
     */
    public function shouldFailForInvalidCompanyType(): void
    {
        $taxIdentity = TaxIdentity::fromCorporateCompany(
            new CorporateTaxId(GeneratedValues::CORPORATE_TAX_ID_VALID_EXAMPLE),
            'Anville',
            '2000'
        );
        $this->expectException(InvalidCompanyType::class);
        new CompanyInfo($taxIdentity, 'Lorax Inc', 'Lorax', 'invalid-partnership');
    }
}
