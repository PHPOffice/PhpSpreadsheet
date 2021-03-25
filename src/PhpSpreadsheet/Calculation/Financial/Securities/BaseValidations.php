<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Constants as SecuritiesConstants;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

abstract class BaseValidations
{
    protected static function validateInputDate($date)
    {
        $date = DateTime::getDateValue($date);
        if (is_string($date)) {
            throw new Exception(Functions::VALUE());
        }

        return $date;
    }

    protected static function validateSettlementDate($settlement)
    {
        return self::validateInputDate($settlement);
    }

    protected static function validateMaturityDate($maturity)
    {
        return self::validateInputDate($maturity);
    }

    protected static function validateIssueDate($issue)
    {
        return self::validateInputDate($issue);
    }

    protected static function validateSecurityPeriod($settlement, $maturity): void
    {
        if ($settlement >= $maturity) {
            throw new Exception(Functions::NAN());
        }
    }

    protected static function validateRate($rate): float
    {
        if (!is_numeric($rate)) {
            throw new Exception(Functions::VALUE());
        }

        $rate = (float) $rate;
        if ($rate < 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $rate;
    }

    protected static function validatePrice($price): float
    {
        if (!is_numeric($price)) {
            throw new Exception(Functions::VALUE());
        }

        $price = (float) $price;
        if ($price < 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $price;
    }

    protected static function validateYield($yield): float
    {
        if (!is_numeric($yield)) {
            throw new Exception(Functions::VALUE());
        }

        $yield = (float) $yield;
        if ($yield < 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $yield;
    }

    protected static function validateRedemption($redemption): float
    {
        if (!is_numeric($redemption)) {
            throw new Exception(Functions::VALUE());
        }

        $redemption = (float) $redemption;
        if ($redemption <= 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $redemption;
    }

    protected static function validateDiscount($discount): float
    {
        if (!is_numeric($discount)) {
            throw new Exception(Functions::VALUE());
        }

        $discount = (float) $discount;
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
