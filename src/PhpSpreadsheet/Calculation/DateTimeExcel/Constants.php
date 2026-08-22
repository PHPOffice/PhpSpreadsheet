<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

class Constants
{
    // Constants currently used by WeekNum; will eventually be used by WEEKDAY
    const STARTWEEK_SUNDAY = 1;
    const STARTWEEK_MONDAY = 2;
    const STARTWEEK_MONDAY_ALT = 11;
    const STARTWEEK_TUESDAY = 12;
    const STARTWEEK_WEDNESDAY = 13;
    const STARTWEEK_THURSDAY = 14;
    const STARTWEEK_FRIDAY = 15;
    const STARTWEEK_SATURDAY = 16;
    const STARTWEEK_SUNDAY_ALT = 17;
    const DOW_SUNDAY = 1;
    const DOW_MONDAY = 2;
    const DOW_TUESDAY = 3;
    const DOW_WEDNESDAY = 4;
    const DOW_THURSDAY = 5;
    const DOW_FRIDAY = 6;
    const DOW_SATURDAY = 7;
    const STARTWEEK_MONDAY_ISO = 21;

    const METHODARR = [
        self::STARTWEEK_SUNDAY => self::DOW_SUNDAY,
        self::DOW_MONDAY,
        self::STARTWEEK_MONDAY_ALT => self::DOW_MONDAY,
        self::DOW_TUESDAY,
        self::DOW_WEDNESDAY,
        self::DOW_THURSDAY,
        self::DOW_FRIDAY,
        self::DOW_SATURDAY,
        self::DOW_SUNDAY,
        self::STARTWEEK_MONDAY_ISO => self::STARTWEEK_MONDAY_ISO,
    ];
}
