<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Variable;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class NonPeriodic
{
    const FINANCIAL_MAX_ITERATIONS = 128;

    const FINANCIAL_PRECISION = 1.0e-08;

    /**
     * XIRR.
     *
     * Returns the internal rate of return for a schedule of cash flows that is not necessarily periodic.
     *
     * Excel Function:
     *        =XIRR(values,dates,guess)
     *
     * @param float[] $values     A series of cash flow payments
     *                                The series of values must contain at least one positive value & one negative value
     * @param mixed[] $dates      A series of payment dates
     *                                The first payment date indicates the beginning of the schedule of payments
     *                                All other dates must be later than this date, but they may occur in any order
     * @param float $guess        An optional guess at the expected answer
     *
     * @return float|string
     */
    public static function rate($values, $dates, $guess = 0.1)
    {
        $rslt = self::xirrPart1($values, $dates);
        if ($rslt) {
            return $rslt;
        }

        // create an initial range, with a root somewhere between 0 and guess
        $guess = Functions::flattenSingleValue($guess);
        $x1 = 0.0;
        $x2 = $guess ?: 0.1;
        $f1 = self::xnpvOrdered($x1, $values, $dates, false);
        $f2 = self::xnpvOrdered($x2, $values, $dates, false);
        $found = false;
        for ($i = 0; $i < self::FINANCIAL_MAX_ITERATIONS; ++$i) {
            if (!is_numeric($f1) || !is_numeric($f2)) {
                break;
            }
            if (($f1 * $f2) < 0.0) {
                $found = true;

                break;
            } elseif (abs($f1) < abs($f2)) {
                $f1 = self::xnpvOrdered($x1 += 1.6 * ($x1 - $x2), $values, $dates, false);
            } else {
                $f2 = self::xnpvOrdered($x2 += 1.6 * ($x2 - $x1), $values, $dates, false);
            }
        }
        if (!$found) {
            return Functions::NAN();
        }

        return self::xirrPart3($values, $dates, $x1, $x2);
    }

    /**
     * XNPV.
     *
     * Returns the net present value for a schedule of cash flows that is not necessarily periodic.
     * To calculate the net present value for a series of cash flows that is periodic, use the NPV function.
     *
     * Excel Function:
     *        =XNPV(rate,values,dates)
     *
     * @param float $rate the discount rate to apply to the cash flows
     * @param float[] $values A series of cash flows that corresponds to a schedule of payments in dates.
     *                          The first payment is optional and corresponds to a cost or payment that occurs
     *                              at the beginning of the investment.
     *                          If the first value is a cost or payment, it must be a negative value.
     *                             All succeeding payments are discounted based on a 365-day year.
     *                          The series of values must contain at least one positive value and one negative value.
     * @param mixed[] $dates A schedule of payment dates that corresponds to the cash flow payments.
     *                         The first payment date indicates the beginning of the schedule of payments.
     *                         All other dates must be later than this date, but they may occur in any order.
     *
     * @return float|string
     */
    public static function presentValue($rate, $values, $dates)
    {
        return self::xnpvOrdered($rate, $values, $dates, true);
    }

    private static function bothNegAndPos($neg, $pos)
    {
        return $neg && $pos;
    }

    private static function xirrPart1(&$values, &$dates)
    {
        if ((!is_array($values)) && (!is_array($dates))) {
            return Functions::NA();
        }
        $values = Functions::flattenArray($values);
        $dates = Functions::flattenArray($dates);
        if (count($values) != count($dates)) {
            return Functions::NAN();
        }

        $datesCount = count($dates);
        for ($i = 0; $i < $datesCount; ++$i) {
            try {
                $dates[$i] = DateTimeExcel\Helpers::getDateValue($dates[$i]);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }

        return self::xirrPart2($values);
    }

    private static function xirrPart2(&$values)
    {
        $valCount = count($values);
        $foundpos = false;
        $foundneg = false;
        for ($i = 0; $i < $valCount; ++$i) {
            $fld = $values[$i];
            if (!is_numeric($fld)) {
                return Functions::VALUE();
            } elseif ($fld > 0) {
                $foundpos = true;
            } elseif ($fld < 0) {
                $foundneg = true;
            }
        }
        if (!self::bothNegAndPos($foundneg, $foundpos)) {
            return Functions::NAN();
        }

        return '';
    }

    private static function xirrPart3($values, $dates, $x1, $x2)
    {
        $f = self::xnpvOrdered($x1, $values, $dates, false);
        if ($f < 0.0) {
            $rtb = $x1;
            $dx = $x2 - $x1;
        } else {
            $rtb = $x2;
            $dx = $x1 - $x2;
        }

        $rslt = Functions::VALUE();
        for ($i = 0; $i < self::FINANCIAL_MAX_ITERATIONS; ++$i) {
            $dx *= 0.5;
            $x_mid = $rtb + $dx;
            $f_mid = self::xnpvOrdered($x_mid, $values, $dates, false);
            if ($f_mid <= 0.0) {
                $rtb = $x_mid;
            }
            if ((abs($f_mid) < self::FINANCIAL_PRECISION) || (abs($dx) < self::FINANCIAL_PRECISION)) {
                $rslt = $x_mid;

                break;
            }
        }

        return $rslt;
    }

    private static function xnpvOrdered($rate, $values, $dates, $ordered = true)
    {
        $rate = Functions::flattenSingleValue($rate);
        $values = Functions::flattenArray($values);
        $dates = Functions::flattenArray($dates);
        $valCount = count($values);

        try {
            self::validateXnpv($rate, $values, $dates);
            $date0 = DateTimeExcel\Helpers::getDateValue($dates[0]);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $xnpv = 0.0;
        for ($i = 0; $i < $valCount; ++$i) {
            if (!is_numeric($values[$i])) {
                return Functions::VALUE();
            }

            try {
                $datei = DateTimeExcel\Helpers::getDateValue($dates[$i]);
            } catch (Exception $e) {
                return $e->getMessage();
            }
            if ($date0 > $datei) {
                $dif = $ordered ? Functions::NAN() : -((int) DateTimeExcel\Difference::interval($datei, $date0, 'd'));
            } else {
                $dif = DateTimeExcel\Difference::interval($date0, $datei, 'd');
            }
            if (!is_numeric($dif)) {
                return $dif;
            }
            $xnpv += $values[$i] / (1 + $rate) ** ($dif / 365);
        }

        return is_finite($xnpv) ? $xnpv : Functions::VALUE();
    }

    private static function validateXnpv($rate, $values, $dates): void
    {
        if (!is_numeric($rate)) {
            throw new Exception(Functions::VALUE());
        }
        $valCount = count($values);
        if ($valCount != count($dates)) {
            throw new Exception(Functions::NAN());
        }
        if ($valCount > 1 && ((min($values) > 0) || (max($values) < 0))) {
            throw new Exception(Functions::NAN());
        }
    }
}
