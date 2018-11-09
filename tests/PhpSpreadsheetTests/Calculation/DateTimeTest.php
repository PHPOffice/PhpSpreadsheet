<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

/**
 * Class DateTimeTest.
 */
class DateTimeTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerDATE
     *
     * @param mixed $expectedResult
     */
    public function testDATE($expectedResult, ...$args)
    {
        $result = DateTime::DATE(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDATE()
    {
        return require 'data/Calculation/DateTime/DATE.php';
    }

    public function testDATEtoPHP()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_NUMERIC);
        $result = DateTime::DATE(2012, 1, 31);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        self::assertEquals(1327968000, $result, null, 1E-8);
    }

    public function testDATEtoPHPObject()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_OBJECT);
        $result = DateTime::DATE(2012, 1, 31);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        //    Must return an object...
        self::assertInternalType('object', $result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTime'));
        //    ... with the correct value
        self::assertEquals($result->format('d-M-Y'), '31-Jan-2012');
    }

    public function testDATEwith1904Calendar()
    {
        Date::setExcelCalendar(Date::CALENDAR_MAC_1904);
        $result = DateTime::DATE(1918, 11, 11);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
        self::assertEquals($result, 5428);
    }

    public function testDATEwith1904CalendarError()
    {
        Date::setExcelCalendar(Date::CALENDAR_MAC_1904);
        $result = DateTime::DATE(1901, 1, 31);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
        self::assertEquals($result, '#NUM!');
    }

    /**
     * @dataProvider providerDATEVALUE
     *
     * @param mixed $expectedResult
     */
    public function testDATEVALUE($expectedResult, ...$args)
    {
        $result = DateTime::DATEVALUE(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDATEVALUE()
    {
        return require 'data/Calculation/DateTime/DATEVALUE.php';
    }

    public function testDATEVALUEtoPHP()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_NUMERIC);
        $result = DateTime::DATEVALUE('2012-1-31');
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        self::assertEquals(1327968000, $result, null, 1E-8);
    }

    public function testDATEVALUEtoPHPObject()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_OBJECT);
        $result = DateTime::DATEVALUE('2012-1-31');
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        //    Must return an object...
        self::assertInternalType('object', $result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTime'));
        //    ... with the correct value
        self::assertEquals($result->format('d-M-Y'), '31-Jan-2012');
    }

    /**
     * @dataProvider providerYEAR
     *
     * @param mixed $expectedResult
     */
    public function testYEAR($expectedResult, ...$args)
    {
        $result = DateTime::YEAR(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerYEAR()
    {
        return require 'data/Calculation/DateTime/YEAR.php';
    }

    /**
     * @dataProvider providerMONTH
     *
     * @param mixed $expectedResult
     */
    public function testMONTH($expectedResult, ...$args)
    {
        $result = DateTime::MONTHOFYEAR(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerMONTH()
    {
        return require 'data/Calculation/DateTime/MONTH.php';
    }

    /**
     * @dataProvider providerWEEKNUM
     *
     * @param mixed $expectedResult
     */
    public function testWEEKNUM($expectedResult, ...$args)
    {
        $result = DateTime::WEEKNUM(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerWEEKNUM()
    {
        return require 'data/Calculation/DateTime/WEEKNUM.php';
    }

    /**
     * @dataProvider providerISOWEEKNUM
     *
     * @param mixed $expectedResult
     * @param mixed $dateValue
     */
    public function testISOWEEKNUM($expectedResult, $dateValue)
    {
        $result = DateTime::ISOWEEKNUM($dateValue);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerISOWEEKNUM()
    {
        return require 'data/Calculation/DateTime/ISOWEEKNUM.php';
    }

    /**
     * @dataProvider providerWEEKDAY
     *
     * @param mixed $expectedResult
     */
    public function testWEEKDAY($expectedResult, ...$args)
    {
        $result = DateTime::WEEKDAY(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerWEEKDAY()
    {
        return require 'data/Calculation/DateTime/WEEKDAY.php';
    }

    /**
     * @dataProvider providerDAY
     *
     * @param mixed $expectedResultExcel
     * @param mixed $expectedResultOpenOffice
     */
    public function testDAY($expectedResultExcel, $expectedResultOpenOffice, ...$args)
    {
        $resultExcel = DateTime::DAYOFMONTH(...$args);
        self::assertEquals($expectedResultExcel, $resultExcel, null, 1E-8);

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
        $resultOpenOffice = DateTime::DAYOFMONTH(...$args);
        self::assertEquals($expectedResultOpenOffice, $resultOpenOffice, null, 1E-8);
    }

    public function providerDAY()
    {
        return require 'data/Calculation/DateTime/DAY.php';
    }

    /**
     * @dataProvider providerTIME
     *
     * @param mixed $expectedResult
     */
    public function testTIME($expectedResult, ...$args)
    {
        $result = DateTime::TIME(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerTIME()
    {
        return require 'data/Calculation/DateTime/TIME.php';
    }

    public function testTIMEtoPHP()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_NUMERIC);
        $result = DateTime::TIME(7, 30, 20);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        self::assertEquals(27020, $result, null, 1E-8);
    }

    public function testTIMEtoPHPObject()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_OBJECT);
        $result = DateTime::TIME(7, 30, 20);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        //    Must return an object...
        self::assertInternalType('object', $result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTime'));
        //    ... with the correct value
        self::assertEquals($result->format('H:i:s'), '07:30:20');
    }

    /**
     * @dataProvider providerTIMEVALUE
     *
     * @param mixed $expectedResult
     */
    public function testTIMEVALUE($expectedResult, ...$args)
    {
        $result = DateTime::TIMEVALUE(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerTIMEVALUE()
    {
        return require 'data/Calculation/DateTime/TIMEVALUE.php';
    }

    public function testTIMEVALUEtoPHP()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_NUMERIC);
        $result = DateTime::TIMEVALUE('7:30:20');
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        self::assertEquals(23420, $result, null, 1E-8);
    }

    public function testTIMEVALUEtoPHPObject()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_OBJECT);
        $result = DateTime::TIMEVALUE('7:30:20');
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        //    Must return an object...
        self::assertInternalType('object', $result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTime'));
        //    ... with the correct value
        self::assertEquals($result->format('H:i:s'), '07:30:20');
    }

    /**
     * @dataProvider providerHOUR
     *
     * @param mixed $expectedResult
     */
    public function testHOUR($expectedResult, ...$args)
    {
        $result = DateTime::HOUROFDAY(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerHOUR()
    {
        return require 'data/Calculation/DateTime/HOUR.php';
    }

    /**
     * @dataProvider providerMINUTE
     *
     * @param mixed $expectedResult
     */
    public function testMINUTE($expectedResult, ...$args)
    {
        $result = DateTime::MINUTE(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerMINUTE()
    {
        return require 'data/Calculation/DateTime/MINUTE.php';
    }

    /**
     * @dataProvider providerSECOND
     *
     * @param mixed $expectedResult
     */
    public function testSECOND($expectedResult, ...$args)
    {
        $result = DateTime::SECOND(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerSECOND()
    {
        return require 'data/Calculation/DateTime/SECOND.php';
    }

    /**
     * @dataProvider providerNETWORKDAYS
     *
     * @param mixed $expectedResult
     */
    public function testNETWORKDAYS($expectedResult, ...$args)
    {
        $result = DateTime::NETWORKDAYS(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerNETWORKDAYS()
    {
        return require 'data/Calculation/DateTime/NETWORKDAYS.php';
    }

    /**
     * @dataProvider providerWORKDAY
     *
     * @param mixed $expectedResult
     */
    public function testWORKDAY($expectedResult, ...$args)
    {
        $result = DateTime::WORKDAY(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerWORKDAY()
    {
        return require 'data/Calculation/DateTime/WORKDAY.php';
    }

    /**
     * @dataProvider providerEDATE
     *
     * @param mixed $expectedResult
     */
    public function testEDATE($expectedResult, ...$args)
    {
        $result = DateTime::EDATE(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerEDATE()
    {
        return require 'data/Calculation/DateTime/EDATE.php';
    }

    public function testEDATEtoPHP()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_NUMERIC);
        $result = DateTime::EDATE('2012-1-26', -1);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        self::assertEquals(1324857600, $result, null, 1E-8);
    }

    public function testEDATEtoPHPObject()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_OBJECT);
        $result = DateTime::EDATE('2012-1-26', -1);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        //    Must return an object...
        self::assertInternalType('object', $result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTime'));
        //    ... with the correct value
        self::assertEquals($result->format('d-M-Y'), '26-Dec-2011');
    }

    /**
     * @dataProvider providerEOMONTH
     *
     * @param mixed $expectedResult
     */
    public function testEOMONTH($expectedResult, ...$args)
    {
        $result = DateTime::EOMONTH(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerEOMONTH()
    {
        return require 'data/Calculation/DateTime/EOMONTH.php';
    }

    public function testEOMONTHtoPHP()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_NUMERIC);
        $result = DateTime::EOMONTH('2012-1-26', -1);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        self::assertEquals(1325289600, $result, null, 1E-8);
    }

    public function testEOMONTHtoPHPObject()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_OBJECT);
        $result = DateTime::EOMONTH('2012-1-26', -1);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        //    Must return an object...
        self::assertInternalType('object', $result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTime'));
        //    ... with the correct value
        self::assertEquals($result->format('d-M-Y'), '31-Dec-2011');
    }

    /**
     * @dataProvider providerDATEDIF
     *
     * @param mixed $expectedResult
     */
    public function testDATEDIF($expectedResult, ...$args)
    {
        $result = DateTime::DATEDIF(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDATEDIF()
    {
        return require 'data/Calculation/DateTime/DATEDIF.php';
    }

    /**
     * @dataProvider providerDAYS
     *
     * @param mixed $expectedResult
     */
    public function testDAYS($expectedResult, ...$args)
    {
        $result = DateTime::DAYS(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDAYS()
    {
        return require 'data/Calculation/DateTime/DAYS.php';
    }

    /**
     * @dataProvider providerDAYS360
     *
     * @param mixed $expectedResult
     */
    public function testDAYS360($expectedResult, ...$args)
    {
        $result = DateTime::DAYS360(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDAYS360()
    {
        return require 'data/Calculation/DateTime/DAYS360.php';
    }

    /**
     * @dataProvider providerYEARFRAC
     *
     * @param mixed $expectedResult
     */
    public function testYEARFRAC($expectedResult, ...$args)
    {
        $result = DateTime::YEARFRAC(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerYEARFRAC()
    {
        return require 'data/Calculation/DateTime/YEARFRAC.php';
    }
}
