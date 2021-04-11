<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\FinancialValidations;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Constants;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class CashFlowValidations extends FinancialValidations
{
    /**
     * @param mixed $rate
     * @throws Exception
     */
    public static function validateRate($rate): float
    {
        $rate = self::validateFloat($rate);
        if ($rate < 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $rate;
    }

    /**
     * @param mixed $type
     * @throws Exception
     */
    public static function validatePeriodType($type): int
    {
        $rate = self::validateInt($type);
        if ($type !== Constants::END_OF_PERIOD && $type !== Constants::BEGINNING_OF_PERIOD) {
            throw new Exception(Functions::NAN());
        }

        return $rate;
    }

    /**
     * @param mixed $presentValue
     * @throws Exception
     */
    public static function validatePresentValue($presentValue): float
    {
        return self::validateFloat($presentValue);
    }

    /**
     * @param mixed $futureValue
     * @throws Exception
     */
    public static function validateFutureValue($futureValue): float
    {
        return self::validateFloat($futureValue);
    }
}
