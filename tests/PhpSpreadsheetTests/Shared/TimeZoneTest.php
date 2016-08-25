<?php

namespace PhpSpreadsheetTests\Shared;

use PhpSpreadsheet\Shared\TimeZone;

class TimeZoneTest extends \PHPUnit_Framework_TestCase
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
            $result = call_user_func([TimeZone::class, 'setTimezone'], $timezoneValue);
            $this->assertTrue($result);
        }
    }

    public function testSetTimezoneWithInvalidValue()
    {
        $unsupportedTimezone = 'Etc/GMT+10';
        $result = call_user_func([TimeZone::class, 'setTimezone'], $unsupportedTimezone);
        $this->assertFalse($result);
    }
}
