<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Variable;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Periodic
{
    const FINANCIAL_MAX_ITERATIONS = 128;

    const FINANCIAL_PRECISION = 1.0e-08;

    /**
     * IRR.
     *
     * Returns the internal rate of return for a series of cash flows represented by the numbers in values.
     * These cash flows do not have to be even, as they would be for an annuity. However, the cash flows must occur
     * at regular intervals, such as monthly or annually. The internal rate of return is the interest rate received
     * for an investment consisting of payments (negative values) and income (positive values) that occur at regular
     * periods.
     *
     * Excel Function:
     *        IRR(values[,guess])
     *
     * @param mixed $values An array or a reference to cells that contain numbers for which you want
     *                                    to calculate the internal rate of return.
     *                                Values must contain at least one positive value and one negative value to
     *                                    calculate the internal rate of return.
     * @param mixed $guess A number that you guess is close to the result of IRR
     */
    public static function rate(mixed $values, mixed $guess = 0.1): string|float
    {
        if (!is_array($values)) {
            return ExcelError::VALUE();
        }
        $values = Functions::flattenArray($values);
        $guess = Functions::flattenSingleValue($guess);
        if (!is_numeric($guess)) {
            return ExcelError::VALUE();
        }

        // create an initial range, with a root somewhere between 0 and guess
        $x1 = 0.0;
        $x2 = $guess;
        $f1 = self::presentValue($x1, $values);
        $f2 = self::presentValue($x2, $values);
        for ($i = 0; $i < self::FINANCIAL_MAX_ITERATIONS; ++$i) {
            if (($f1 * $f2) < 0.0) {
                break;
            }
            if (abs($f1) < abs($f2)) {
                $f1 = self::presentValue($x1 += 1.6 * ($x1 - $x2), $values);
            } else {
                $f2 = self::presentValue($x2 += 1.6 * ($x2 - $x1), $values);
            }
        }
        if (($f1 * $f2) > 0.0) {
            return ExcelError::VALUE();
        }

        $f = self::presentValue($x1, $values);
        if ($f < 0.0) {
            $rtb = $x1;
            $dx = $x2 - $x1;
        } else {
            $rtb = $x2;
            $dx = $x1 - $x2;
        }

        for ($i = 0; $i < self::FINANCIAL_MAX_ITERATIONS; ++$i) {
            $dx *= 0.5;
            $x_mid = $rtb + $dx;
            $f_mid = self::presentValue($x_mid, $values);
            if ($f_mid <= 0.0) {
                $rtb = $x_mid;
            }
            if ((abs($f_mid) < self::FINANCIAL_PRECISION) || (abs($dx) < self::FINANCIAL_PRECISION)) {
                return $x_mid;
            }
        }

        return ExcelError::VALUE();
    }

    /**
     * MIRR.
     *
     * Returns the modified internal rate of return for a series of periodic cash flows. MIRR considers both
     *        the cost of the investment and the interest received on reinvestment of cash.
     *
     * Excel Function:
     *        MIRR(values,finance_rate, reinvestment_rate)
     *
     * @param mixed $values An array or a reference to cells that contain a series of payments and
     *                         income occurring at regular intervals.
     *                      Payments are negative value, income is positive values.
     * @param mixed $financeRate The interest rate you pay on the money used in the cash flows
     * @param mixed $reinvestmentRate The interest rate you receive on the cash flows as you reinvest them
     *
     * @return float|string Result, or a string containing an error
     */
    public static function modifiedRate(mixed $values, mixed $financeRate, mixed $reinvestmentRate): string|float
    {
        if (!is_array($values)) {
            return ExcelError::DIV0();
        }
        $values = Functions::flattenArray($values);
        /** @var float */
        $financeRate = Functions::flattenSingleValue($financeRate);
        /** @var float */
        $reinvestmentRate = Functions::flattenSingleValue($reinvestmentRate);
        $n = count($values);

        $rr = 1.0 + $reinvestmentRate;
        $fr = 1.0 + $financeRate;

        $npvPos = $npvNeg = 0.0;
        foreach ($values as $i => $v) {
            if ($v >= 0) {
                $npvPos += $v / $rr ** $i;
            } else {
                $npvNeg += $v / $fr ** $i;
            }
        }

        if ($npvNeg === 0.0 || $npvPos === 0.0) {
            return ExcelError::DIV0();
        }

        $mirr = ((-$npvPos * $rr ** $n)
                / ($npvNeg * ($rr))) ** (1.0 / ($n - 1)) - 1.0;

        return is_finite($mirr) ? $mirr : ExcelError::NAN();
    }

    /**
     * NPV.
     *
     * Returns the Net Present Value of a cash flow series given a discount rate.
     *
     * @param array $args
     */
    public static function presentValue(mixed $rate, ...$args): int|float
    {
        $returnValue = 0;

        /** @var float */
        $rate = Functions::flattenSingleValue($rate);
        $aArgs = Functions::flattenArray($args);

        // Calculate
        $countArgs = count($aArgs);
        for ($i = 1; $i <= $countArgs; ++$i) {
            // Is it a numeric value?
            if (is_numeric($aArgs[$i - 1])) {
                $returnValue += $aArgs[$i - 1] / (1 + $rate) ** $i;
            }
        }

        return $returnValue;
    }
}
