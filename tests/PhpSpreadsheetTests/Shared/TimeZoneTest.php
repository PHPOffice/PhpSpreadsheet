<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\TimeZone;
use PHPUnit\Framework\TestCase;

class TimeZoneTest extends TestCase
{
    public function testSetTimezone()
    {
        $timezoneValues = [
            'Europe/Prague',
            'Asia/Tokyo',
            'America/Indiana/Indianapolis',
            'Pacific/Honolulu',
            'Atlantic/St_Helena',
        ];

        foreach ($timezoneValues as $timezoneValue) {
            $result = TimeZone::setTimezone($timezoneValue);
            self::assertTrue($result);
        }
    }

    public function testSetTimezoneWithInvalidValue()
    {
        $unsupportedTimezone = 'Etc/GMT+10';
        $result = TimeZone::setTimezone($unsupportedTimezone);
        self::assertFalse($result);
    }
}
