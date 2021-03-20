<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class WeekDay
{
    /**
     * WEEKDAY.
     *
     * Returns the day of the week for a specified date. The day is given as an integer
     * ranging from 0 to 7 (dependent on the requested style).
     *
     * Excel Function:
     *        WEEKDAY(dateValue[,style])
     *
     * @param float|int|string $dateValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     * @param int $style A number that determines the type of return value
     *                                        1 or omitted    Numbers 1 (Sunday) through 7 (Saturday).
     *                                        2                Numbers 1 (Monday) through 7 (Sunday).
     *                                        3                Numbers 0 (Monday) through 6 (Sunday).
     *
     * @return int|string Day of the week value
     */
    public static function funcWeekDay($dateValue, $style = 1)
    {
        try {
            $dateValue = Helpers::getDateValue($dateValue);
            $style = self::validateStyle($style);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Execute function
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);
        Helpers::silly1900($PHPDateObject);
        $DoW = (int) $PHPDateObject->format('w');

        switch ($style) {
            case 1:
                ++$DoW;

                break;
            case 2:
                $DoW = self::dow0Becomes7($DoW);

                break;
            case 3:
                $DoW = self::dow0Becomes7($DoW) - 1;

                break;
        }

        return $DoW;
    }

    private static function validateStyle($style): int
    {
        $style = Functions::flattenSingleValue($style);

        if (!is_numeric($style)) {
            throw new Exception(Functions::VALUE());
        }
        $style = (int) $style;
        if (($style < 1) || ($style > 3)) {
            throw new Exception(Functions::NAN());
        }

        return $style;
    }

    private static function dow0Becomes7(int $DoW): int
    {
        return ($DoW === 0) ? 7 : $DoW;
    }
}
