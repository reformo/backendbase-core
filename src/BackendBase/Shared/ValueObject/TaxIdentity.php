<?php

declare(strict_types=1);

namespace Reformo\Shared\ValueObject;

final class TaxIdentity
{
    public const TAXPAYER_PRIVATE   = 'private';
    public const TAXPAYER_CORPORATE = 'corporate';
    private string $taxIdNumber;
    private string $taxAdministrationOfficeName;
    private string $taxAdministrationOfficeCode;
    private string $taxPayerType;

    private function __construct(
        string $taxIdNumber,
        string $taxAdministrationOfficeName,
        string $taxAdministrationOfficeCode,
        string $taxPayerType
    ) {
        $this->taxIdNumber                 = $taxIdNumber;
        $this->taxAdministrationOfficeName = $taxAdministrationOfficeName;
        $this->taxAdministrationOfficeCode = $taxAdministrationOfficeCode;
        $this->taxPayerType                = $taxPayerType;
    }

    public static function fromPrivateCompany(
        PrivateTaxId $taxId,
        string $taxAdministrationOfficeName,
        string $taxAdministrationOfficeCode
    ) : self {
        return new self(
            $taxId->taxId(),
            $taxAdministrationOfficeName,
            $taxAdministrationOfficeCode,
            self::TAXPAYER_PRIVATE
        );
    }

    public static function fromCorporateCompany(
        CorporateTaxId $taxId,
        string $taxAdministrationOfficeName,
        string $taxAdministrationOfficeCode
    ) : self {
        return new self(
            $taxId->taxId(),
            $taxAdministrationOfficeName,
            $taxAdministrationOfficeCode,
            self::TAXPAYER_CORPORATE
        );
    }

    public function getTaxIdNumber() : string
    {
        return $this->taxIdNumber;
    }

    public function getTaxAdministrationOfficeName() : string
    {
        return $this->taxAdministrationOfficeName;
    }

    public function getTaxAdministrationOfficeCode() : string
    {
        return $this->taxAdministrationOfficeCode;
    }

    public function getTaxPayerType() : string
    {
        return $this->taxPayerType;
    }
}
