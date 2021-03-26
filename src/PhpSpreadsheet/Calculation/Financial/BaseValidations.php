<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Constants as SecuritiesConstants;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

trait BaseValidations
{
    protected static function validateDate($date)
    {
        $date = DateTime::getDateValue($date);
        if (is_string($date)) {
            throw new Exception(Functions::VALUE());
        }

        return $date;
    }

    protected static function validateFloat($value): float
    {
        if (!is_numeric($value)) {
            throw new Exception(Functions::VALUE());
        }

        return (float) $value;
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
