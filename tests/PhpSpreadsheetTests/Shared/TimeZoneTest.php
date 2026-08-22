<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use DateTime;
use DateTimeZone;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\TimeZone;
use PHPUnit\Framework\TestCase;

class TimeZoneTest extends TestCase
{
    private string $tztimezone;

    private ?DateTimeZone $dttimezone;

    protected function setUp(): void
    {
        $this->tztimezone = TimeZone::getTimeZone();
        $this->dttimezone = Date::getDefaultTimeZoneOrNull();
    }

    protected function tearDown(): void
    {
        TimeZone::setTimeZone($this->tztimezone);
        Date::setDefaultTimeZone($this->dttimezone);
    }

    public function testSetTimezone(): void
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
            $result = Date::setDefaultTimezone($timezoneValue);
            self::assertTrue($result);
        }
    }

    public function testSetTimezoneBackwardCompatible(): void
    {
        $bcTimezone = 'Etc/GMT-10';
        $result = TimeZone::setTimezone($bcTimezone);
        self::assertTrue($result);
        $result = Date::setDefaultTimezone($bcTimezone);
        self::assertTrue($result);
    }

    public function testSetTimezoneWithInvalidValue(): void
    {
        $unsupportedTimezone = 'XEtc/GMT+10';
        $result = TimeZone::setTimezone($unsupportedTimezone);
        self::assertFalse($result);
        $result = Date::setDefaultTimezone($unsupportedTimezone);
        self::assertFalse($result);
    }

    public function testTimeZoneAdjustmentsInvalidTz(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        $dtobj = DateTime::createFromFormat('Y-m-d H:i:s', '2008-09-22 00:00:00');
        if ($dtobj === false) {
            self::fail('DateTime createFromFormat failed');
        } else {
            $tstmp = $dtobj->getTimestamp();
            $unsupportedTimeZone = 'XEtc/GMT+10';
            TimeZone::getTimeZoneAdjustment($unsupportedTimeZone, $tstmp);
        }
    }

    public function testTimeZoneAdjustments(): void
    {
        $dtobj = DateTime::createFromFormat('Y-m-d H:i:s', '2008-01-01 00:00:00');
        if ($dtobj === false) {
            self::fail('DateTime createFromFormat failed');
        } else {
            $tstmp = $dtobj->getTimestamp();
            $supportedTimeZone = 'UTC';
            $adj = TimeZone::getTimeZoneAdjustment($supportedTimeZone, $tstmp);
            self::assertEquals(0, $adj);
            $supportedTimeZone = 'America/Toronto';
            $adj = TimeZone::getTimeZoneAdjustment($supportedTimeZone, $tstmp);
            self::assertEquals(-18000, $adj);
            $supportedTimeZone = 'America/Chicago';
            TimeZone::setTimeZone($supportedTimeZone);
            $adj = TimeZone::getTimeZoneAdjustment(null, $tstmp);
            self::assertEquals(-21600, $adj);
        }
    }
}
