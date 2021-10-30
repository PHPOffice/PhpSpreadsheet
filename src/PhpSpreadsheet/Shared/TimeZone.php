<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use DateTimeZone;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class TimeZone
{
    /**
     * Default Timezone used for date/time conversions.
     *
     * @var string
     */
    protected static $timezone = 'UTC';

    /**
     * Validate a Timezone name.
     *
     * @param string $timezone Time zone (e.g. 'Europe/London')
     *
     * @return bool Success or failure
     */
    private static function validateTimeZone($timezone)
    {
        return in_array($timezone, DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC));
    }

    /**
     * Set the Default Timezone used for date/time conversions.
     *
     * @param string $timezone Time zone (e.g. 'Europe/London')
     *
     * @return bool Success or failure
     */
    public static function setTimeZone($timezone)
    {
        if (self::validateTimezone($timezone)) {
            self::$timezone = $timezone;

            return true;
        }

        return false;
    }

    /**
     * Return the Default Timezone used for date/time conversions.
     *
     * @return string Timezone (e.g. 'Europe/London')
     */
    public static function getTimeZone()
    {
        return self::$timezone;
    }

    /**
     *    Return the Timezone offset used for date/time conversions to/from UST
     * This requires both the timezone and the calculated date/time to allow for local DST.
     *
     * @param ?string $timezone The timezone for finding the adjustment to UST
     * @param float|int $timestamp PHP date/time value
     *
     * @return int Number of seconds for timezone adjustment
     */
    public static function getTimeZoneAdjustment($timezone, $timestamp)
    {
        $timezone = $timezone ?? self::$timezone;
        $dtobj = Date::dateTimeFromTimestamp("$timestamp");
        if (!self::validateTimezone($timezone)) {
            throw new PhpSpreadsheetException("Invalid timezone $timezone");
        }
        $dtobj->setTimeZone(new DateTimeZone($timezone));

        return $dtobj->getOffset();
    }
}
