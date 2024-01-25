<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

class Constants
{
    // Constants currently used by WeekNum; will eventually be used by WEEKDAY
    public const STARTWEEK_SUNDAY = 1;
    public const STARTWEEK_MONDAY = 2;
    public const STARTWEEK_MONDAY_ALT = 11;
    public const STARTWEEK_TUESDAY = 12;
    public const STARTWEEK_WEDNESDAY = 13;
    public const STARTWEEK_THURSDAY = 14;
    public const STARTWEEK_FRIDAY = 15;
    public const STARTWEEK_SATURDAY = 16;
    public const STARTWEEK_SUNDAY_ALT = 17;
    public const DOW_SUNDAY = 1;
    public const DOW_MONDAY = 2;
    public const DOW_TUESDAY = 3;
    public const DOW_WEDNESDAY = 4;
    public const DOW_THURSDAY = 5;
    public const DOW_FRIDAY = 6;
    public const DOW_SATURDAY = 7;
    public const STARTWEEK_MONDAY_ISO = 21;

    public const METHODARR = [
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
