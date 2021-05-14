<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Current
{
    /**
     * DATENOW.
     *
     * Returns the current date.
     * The NOW function is useful when you need to display the current date and time on a worksheet or
     * calculate a value based on the current date and time, and have that value updated each time you
     * open the worksheet.
     *
     * NOTE: When used in a Cell Formula, MS Excel changes the cell format so that it matches the date
     * and time format of your regional settings. PhpSpreadsheet does not change cell formatting in this way.
     *
     * Excel Function:
     *        TODAY()
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     */
    public static function today()
    {
        $dti = new DateTimeImmutable();
        $dateArray = date_parse($dti->format('c'));

        return is_array($dateArray) ? Helpers::returnIn3FormatsArray($dateArray, true) : Functions::VALUE();
    }

    /**
     * DATETIMENOW.
     *
     * Returns the current date and time.
     * The NOW function is useful when you need to display the current date and time on a worksheet or
     * calculate a value based on the current date and time, and have that value updated each time you
     * open the worksheet.
     *
     * NOTE: When used in a Cell Formula, MS Excel changes the cell format so that it matches the date
     * and time format of your regional settings. PhpSpreadsheet does not change cell formatting in this way.
     *
     * Excel Function:
     *        NOW()
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     */
    public static function now()
    {
        $dti = new DateTimeImmutable();
        $dateArray = date_parse($dti->format('c'));

        return is_array($dateArray) ? Helpers::returnIn3FormatsArray($dateArray) : Functions::VALUE();
    }
}
