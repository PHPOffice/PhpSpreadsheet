<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Constants as SecuritiesConstants;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

trait BaseValidations
{
    protected static function validateDate($date)
    {
        return DateTimeExcel\Helpers::getDateValue($date);
    }

    protected static function validateFloat($value): float
    {
        if (!is_numeric($value)) {
            throw new Exception(Functions::VALUE());
        }

        return (float) $value;
    }

    protected static function validateSettlementDate($settlement)
    {
        return self::validateDate($settlement);
    }

    protected static function validateMaturityDate($maturity)
    {
        return self::validateDate($maturity);
    }

    protected static function validateIssueDate($issue)
    {
        return self::validateDate($issue);
    }

    protected static function validateSecurityPeriod($settlement, $maturity): void
    {
        if ($settlement >= $maturity) {
            throw new Exception(Functions::NAN());
        }
    }

    protected static function validateRate($rate): float
    {
        $rate = self::validateFloat($rate);
        if ($rate < 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $rate;
    }

    protected static function validateParValue($parValue): float
    {
        $parValue = self::validateFloat($parValue);
        if ($parValue < 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $parValue;
    }

    protected static function validatePrice($price): float
    {
        $price = self::validateFloat($price);
        if ($price < 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $price;
    }

    protected static function validateYield($yield): float
    {
        $yield = self::validateFloat($yield);
        if ($yield < 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $yield;
    }

    protected static function validateRedemption($redemption): float
    {
        $redemption = self::validateFloat($redemption);
        if ($redemption <= 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $redemption;
    }

    protected static function validateDiscount($discount): float
    {
        $discount = self::validateFloat($discount);
        if ($discount <= 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $discount;
    }

    protected static function validateFrequency($frequency): int
    {
        if (!is_numeric($frequency)) {
            throw new Exception(Functions::VALUE());
        }

        $frequency = (int) $frequency;
        if (
            ($frequency !== SecuritiesConstants::FREQUENCY_ANNUAL) &&
            ($frequency !== SecuritiesConstants::FREQUENCY_SEMI_ANNUAL) &&
            ($frequency !== SecuritiesConstants::FREQUENCY_QUARTERLY)
        ) {
            throw new Exception(Functions::NAN());
        }

        return $frequency;
    }

    protected static function validateBasis($basis): int
    {
        if (!is_numeric($basis)) {
            throw new Exception(Functions::VALUE());
        }

        $basis = (int) $basis;
        if (($basis < 0) || ($basis > 4)) {
            throw new Exception(Functions::NAN());
        }

        return $basis;
    }
}
