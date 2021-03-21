<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Sanity tests for functions which have been moved out of DateTime
 * to their own classes. A deprecated version remains in DateTime;
 * this class contains cursory tests to ensure that those work properly.
 * If Scrutinizer fails the PR because of these deprecations, I will
 * remove this class from the PR.
 *
 * @covers \PhpOffice\PhpSpreadsheet\Calculation\DateTime
 */
class MovedFunctionsTest extends TestCase
{
    public function testMovedFunctions(): void
    {
        self::assertTrue(DateTime::isLeapYear(1904));
        self::assertSame('#VALUE!', DateTime::getDateValue('XYZ'));
        self::assertSame(61.0, DateTime::getDateValue('1900-03-01'));
        self::assertSame(61.0, DateTime::DATE(1900, 3, 1));
        self::assertSame(365, DateTime::DATEDIF('2016-01-01', '2016-12-31', 'YD'));
        self::assertSame(61.0, DateTime::DATEVALUE('1900-03-01'));
        self::assertSame(28, DateTime::DAYOFMONTH('1904-02-28'));
        self::assertSame(364, DateTime::DAYS('2007-12-31', '2007-1-1'));
        self::assertSame(9, DateTime::DAYS360('2007-1-1', '2007-1-10', false));
        self::assertSame(39493.0, DateTime::EDATE('15-Jan-2008', 1));
        self::assertSame(39507.0, DateTime::EOMONTH('15-Jan-2008', 1));
        self::assertSame(18, DateTime::HOUROFDAY(0.75));
        self::assertSame(52, DateTime::ISOWEEKNUM('2000-01-01'));
        self::assertSame(24, DateTime::MINUTE(0.6));
        self::assertSame(11, DateTime::MONTHOFYEAR('11-Nov-1918'));
        self::assertSame(8, DateTime::NETWORKDAYS('1-Jan-2007', '10-Jan-2007'));
        self::assertSame(35, DateTime::SECOND('11:15:35'));
        self::assertSame(0.5, DateTime::TIME(12, 0, 0));
        self::assertSame(0.40625, DateTime::TIMEVALUE('33:45'));
        self::assertSame(5, DateTime::WEEKDAY('24-Oct-1968'));
        self::assertSame(52, DateTime::WEEKNUM('21-Dec-2000'));
        self::assertSame(39094.0, DateTime::WORKDAY('1-Jan-2007', 9));
        self::assertSame(1904, DateTime::YEAR('1904-02-28'));
        self::assertSame(0.025, DateTime::YEARFRAC('2007-01-10', '2007-01-01', 0));
    }

    public function testTodayAndNow(): void
    {
        // Loop to avoid rare edge case where first calculation
        // and second do not take place in same second.
        do {
            $dtStart = new DateTimeImmutable();
            $startSecond = $dtStart->format('s');
            $nowResult = DateTime::DATETIMENOW();
            $todayResult = DateTime::DATENOW();
            $dtEnd = new DateTimeImmutable();
            $endSecond = $dtEnd->format('s');
        } while ($startSecond !== $endSecond);
        self::assertSame(DateTime::DAYOFMONTH($nowResult), DateTime::DAYOFMONTH($todayResult));
    }
}
