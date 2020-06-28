<?php

declare(strict_types=1);

namespace BackendBase\Shared\ValueObject;

use BackendBase\Shared\ValueObject\Exception\InvalidCompanyLegalName;
use BackendBase\Shared\ValueObject\Exception\InvalidCompanyShortName;
use BackendBase\Shared\ValueObject\Exception\InvalidCompanyType;
use InvalidArgumentException;
use Webmozart\Assert\Assert;
use function implode;
use function sprintf;

final class CompanyInfo
{
    public const COMPANY_TYPE_PRIVATE     = 'private';
    public const COMPANY_TYPE_LIMITED     = 'limited';
    public const COMPANY_TYPE_CORPORATION = 'corporation';

    private static array $companyTypes = [
        self::COMPANY_TYPE_CORPORATION,
        self::COMPANY_TYPE_LIMITED,
        self::COMPANY_TYPE_PRIVATE,
    ];

    private TaxIdentity $taxIdentity;
    private string $legalName;
    private string $shortName;
    private string $companyType;

    public function __construct(TaxIdentity $taxIdentity, string $legalName, string $shortName, string $companyType)
    {
        try {
            Assert::minLength($legalName, 4);
        } catch (InvalidArgumentException $e) {
            throw InvalidCompanyLegalName::create('Company legal name must be at least 4 characters long');
        }

        try {
            Assert::minLength($shortName, 1);
        } catch (InvalidArgumentException $e) {
            throw InvalidCompanyShortName::create('Company legal name must be at least 1 characters long');
        }

        try {
            Assert::inArray($companyType, self::$companyTypes);
        } catch (InvalidArgumentException $e) {
            throw InvalidCompanyType::create(sprintf(
                'Invalid company type provided: "%s". Possible values are: %s',
                $companyType,
                implode(', ', self::$companyTypes)
            ));
        }

        $this->taxIdentity = $taxIdentity;
        $this->legalName   = $legalName;
        $this->shortName   = $shortName;
        $this->companyType = $companyType;
    }

    public function getTaxIdentity() : TaxIdentity
    {
        return $this->taxIdentity;
    }

    public function getLegalName() : string
    {
        return $this->legalName;
    }

    public function getShortName() : string
    {
        return $this->shortName;
    }

    public function getCompanyType() : string
    {
        return $this->companyType;
    }
}
