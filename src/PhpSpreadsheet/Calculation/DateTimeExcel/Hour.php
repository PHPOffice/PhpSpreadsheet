<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class Hour
{
    /**
     * HOUROFDAY.
     *
     * Returns the hour of a time value.
     * The hour is given as an integer, ranging from 0 (12:00 A.M.) to 23 (11:00 P.M.).
     *
     * Excel Function:
     *        HOUR(timeValue)
     *
     * @param mixed $timeValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard time string
     *
     * @return int|string Hour
     */
    public static function funcHour($timeValue)
    {
        try {
            $timeValue = Functions::flattenSingleValue($timeValue);
            Helpers::nullFalseTrueToNumber($timeValue);
            if (!is_numeric($timeValue)) {
                $timeValue = Helpers::getTimeValue($timeValue);
            }
            Helpers::validateNotNegative($timeValue);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Execute function
        $timeValue = fmod($timeValue, 1);
        $timeValue = Date::excelToDateTimeObject($timeValue);

        return (int) $timeValue->format('H');
    }
}
