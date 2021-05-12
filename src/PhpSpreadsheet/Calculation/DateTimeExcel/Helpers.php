<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDateHelper;

class Helpers
{
    /**
     * Identify if a year is a leap year or not.
     *
     * @param int|string $year The year to test
     *
     * @return bool TRUE if the year is a leap year, otherwise FALSE
     */
    public static function isLeapYear($year): bool
    {
        return (($year % 4) === 0) && (($year % 100) !== 0) || (($year % 400) === 0);
    }

    /**
     * getDateValue.
     *
     * @param mixed $dateValue
     *
     * @return float Excel date/time serial value
     */
    public static function getDateValue($dateValue, bool $allowBool = true): float
    {
        if (is_object($dateValue)) {
            $retval = SharedDateHelper::PHPToExcel($dateValue);
            if (is_bool($retval)) {
                throw new Exception(Functions::VALUE());
            }

            return $retval;
        }

        self::nullFalseTrueToNumber($dateValue, $allowBool);
        if (!is_numeric($dateValue)) {
            $saveReturnDateType = Functions::getReturnDateType();
            Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
            $dateValue = DateValue::fromString($dateValue);
            Functions::setReturnDateType($saveReturnDateType);
            if (!is_numeric($dateValue)) {
                throw new Exception(Functions::VALUE());
            }
        }
        if ($dateValue < 0 && Functions::getCompatibilityMode() !== Functions::COMPATIBILITY_OPENOFFICE) {
            throw new Exception(Functions::NAN());
        }

        return (float) $dateValue;
    }

    /**
     * getTimeValue.
     *
     * @param string $timeValue
     *
     * @return mixed Excel date/time serial value, or string if error
     */
    public static function getTimeValue($timeValue)
    {
        $saveReturnDateType = Functions::getReturnDateType();
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        $timeValue = TimeValue::fromString($timeValue);
        Functions::setReturnDateType($saveReturnDateType);

        return $timeValue;
    }

    /**
     * Adjust date by given months.
     *
     * @param mixed $dateValue
     */
    public static function adjustDateByMonths($dateValue = 0, float $adjustmentMonths = 0): DateTime
    {
        // Execute function
        $PHPDateObject = SharedDateHelper::excelToDateTimeObject($dateValue);
        $oMonth = (int) $PHPDateObject->format('m');
        $oYear = (int) $PHPDateObject->format('Y');

        $adjustmentMonthsString = (string) $adjustmentMonths;
        if ($adjustmentMonths > 0) {
            $adjustmentMonthsString = '+' . $adjustmentMonths;
        }
        if ($adjustmentMonths != 0) {
            $PHPDateObject->modify($adjustmentMonthsString . ' months');
        }
        $nMonth = (int) $PHPDateObject->format('m');
        $nYear = (int) $PHPDateObject->format('Y');

        $monthDiff = ($nMonth - $oMonth) + (($nYear - $oYear) * 12);
        if ($monthDiff != $adjustmentMonths) {
            $adjustDays = (int) $PHPDateObject->format('d');
            $adjustDaysString = '-' . $adjustDays . ' days';
            $PHPDateObject->modify($adjustDaysString);
        }

        return $PHPDateObject;
    }

    /**
     * Help reduce perceived complexity of some tests.
     *
     * @param mixed $value
     * @param mixed $altValue
     */
    public static function replaceIfEmpty(&$value, $altValue): void
    {
        $value = $value ?: $altValue;
    }

    /**
     * Adjust year in ambiguous situations.
     */
    public static function adjustYear(string $testVal1, string $testVal2, string &$testVal3): void
    {
        if (!is_numeric($testVal1) || $testVal1 < 31) {
            if (!is_numeric($testVal2) || $testVal2 < 12) {
                if (is_numeric($testVal3) && $testVal3 < 12) {
                    $testVal3 += 2000;
                }
            }
        }
    }

    /**
     * Return result in one of three formats.
     *
     * @return mixed
     */
    public static function returnIn3FormatsArray(array $dateArray, bool $noFrac = false)
    {
        $retType = Functions::getReturnDateType();
        if ($retType === Functions::RETURNDATE_PHP_DATETIME_OBJECT) {
            return new DateTime(
                $dateArray['year']
                . '-' . $dateArray['month']
                . '-' . $dateArray['day']
                . ' ' . $dateArray['hour']
                . ':' . $dateArray['minute']
                . ':' . $dateArray['second']
            );
        }
        $excelDateValue =
            SharedDateHelper::formattedPHPToExcel(
                $dateArray['year'],
                $dateArray['month'],
                $dateArray['day'],
                $dateArray['hour'],
                $dateArray['minute'],
                $dateArray['second']
            );
        if ($retType === Functions::RETURNDATE_EXCEL) {
            return $noFrac ? floor($excelDateValue) : (float) $excelDateValue;
        }
        // RETURNDATE_UNIX_TIMESTAMP)

        return (int) SharedDateHelper::excelToTimestamp($excelDateValue);
    }

    /**
     * Return result in one of three formats.
     *
     * @return mixed
     */
    public static function returnIn3FormatsFloat(float $excelDateValue)
    {
        $retType = Functions::getReturnDateType();
        if ($retType === Functions::RETURNDATE_EXCEL) {
            return $excelDateValue;
        }
        if ($retType === Functions::RETURNDATE_UNIX_TIMESTAMP) {
            return (int) SharedDateHelper::excelToTimestamp($excelDateValue);
        }
        // RETURNDATE_PHP_DATETIME_OBJECT

        return SharedDateHelper::excelToDateTimeObject($excelDateValue);
    }

    /**
     * Return result in one of three formats.
     *
     * @return mixed
     */
    public static function returnIn3FormatsObject(DateTime $PHPDateObject)
    {
        $retType = Functions::getReturnDateType();
        if ($retType === Functions::RETURNDATE_PHP_DATETIME_OBJECT) {
            return $PHPDateObject;
        }
        if ($retType === Functions::RETURNDATE_EXCEL) {
            return (float) SharedDateHelper::PHPToExcel($PHPDateObject);
        }
        // RETURNDATE_UNIX_TIMESTAMP
        $stamp = SharedDateHelper::PHPToExcel($PHPDateObject);
        $stamp = is_bool($stamp) ? ((int) $stamp) : $stamp;

        return (int) SharedDateHelper::excelToTimestamp($stamp);
    }

    private static function baseDate(): int
    {
        if (Functions::getCompatibilityMode() === Functions::COMPATIBILITY_OPENOFFICE) {
            return 0;
        }
        if (SharedDateHelper::getExcelCalendar() === SharedDateHelper::CALENDAR_MAC_1904) {
            return 0;
        }

        return 1;
    }

    /**
     * Many functions accept null/false/true argument treated as 0/0/1.
     *
     * @param mixed $number
     */
    public static function nullFalseTrueToNumber(&$number, bool $allowBool = true): void
    {
        $number = Functions::flattenSingleValue($number);
        $nullVal = self::baseDate();
        if ($number === null) {
            $number = $nullVal;
        } elseif ($allowBool && is_bool($number)) {
            $number = $nullVal + (int) $number;
        }
    }

    /**
     * Many functions accept null argument treated as 0.
     *
     * @param mixed $number
     *
     * @return float|int
     */
    public static function validateNumericNull($number)
    {
        $number = Functions::flattenSingleValue($number);
        if ($number === null) {
            return 0;
        }
        if (is_int($number)) {
            return $number;
        }
        if (is_numeric($number)) {
            return (float) $number;
        }

        throw new Exception(Functions::VALUE());
    }

    /**
     * Many functions accept null/false/true argument treated as 0/0/1.
     *
     * @param mixed $number
     *
     * @return float
     */
    public static function validateNotNegative($number)
    {
        if (!is_numeric($number)) {
            throw new Exception(Functions::VALUE());
        }
        if ($number >= 0) {
            return (float) $number;
        }

        throw new Exception(Functions::NAN());
    }

    public static function silly1900(DateTime $PHPDateObject, string $mod = '-1 day'): void
    {
        $isoDate = $PHPDateObject->format('c');
        if ($isoDate < '1900-03-01') {
            $PHPDateObject->modify($mod);
        }
    }
}
