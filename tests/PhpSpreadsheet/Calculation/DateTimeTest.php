<?php

namespace PhpSpreadsheet\Tests\Calculation;

use PhpSpreadsheet\Shared\Date;
use PhpSpreadsheet\Calculation\Functions;
use PhpSpreadsheet\Calculation\DateTime;

/**
 * Class DateTimeTest
 */
class DateTimeTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerDATE
     */
    public function testDATE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(DateTime::class, 'DATE'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
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
        $this->assertEquals(1327968000, $result, null, 1E-8);
    }

    public function testDATEtoPHPObject()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_OBJECT);
        $result = DateTime::DATE(2012, 1, 31);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        //    Must return an object...
        $this->assertTrue(is_object($result));
        //    ... of the correct type
        $this->assertTrue(is_a($result, 'DateTime'));
        //    ... with the correct value
        $this->assertEquals($result->format('d-M-Y'), '31-Jan-2012');
    }

    public function testDATEwith1904Calendar()
    {
        Date::setExcelCalendar(Date::CALENDAR_MAC_1904);
        $result = DateTime::DATE(1918, 11, 11);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
        $this->assertEquals($result, 5428);
    }

    public function testDATEwith1904CalendarError()
    {
        Date::setExcelCalendar(Date::CALENDAR_MAC_1904);
        $result = DateTime::DATE(1901, 1, 31);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
        $this->assertEquals($result, '#NUM!');
    }

    /**
     * @dataProvider providerDATEVALUE
     */
    public function testDATEVALUE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(DateTime::class, 'DATEVALUE'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
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
        $this->assertEquals(1327968000, $result, null, 1E-8);
    }

    public function testDATEVALUEtoPHPObject()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_OBJECT);
        $result = DateTime::DATEVALUE('2012-1-31');
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        //    Must return an object...
        $this->assertTrue(is_object($result));
        //    ... of the correct type
        $this->assertTrue(is_a($result, 'DateTime'));
        //    ... with the correct value
        $this->assertEquals($result->format('d-M-Y'), '31-Jan-2012');
    }

    /**
     * @dataProvider providerYEAR
     */
    public function testYEAR()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(DateTime::class, 'YEAR'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerYEAR()
    {
        return require 'data/Calculation/DateTime/YEAR.php';
    }

    /**
     * @dataProvider providerMONTH
     */
    public function testMONTH()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(DateTime::class, 'MONTHOFYEAR'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerMONTH()
    {
        return require 'data/Calculation/DateTime/MONTH.php';
    }

    /**
     * @dataProvider providerWEEKNUM
     * @group fail19
     */
    public function testWEEKNUM()
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(DateTime::class, 'WEEKOFYEAR'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerWEEKNUM()
    {
        return require 'data/Calculation/DateTime/WEEKNUM.php';
    }

    /**
     * @dataProvider providerWEEKDAY
     * @group fail19
     */
    public function testWEEKDAY()
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(DateTime::class, 'DAYOFWEEK'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerWEEKDAY()
    {
        return require 'data/Calculation/DateTime/WEEKDAY.php';
    }

    /**
     * @dataProvider providerDAY
     */
    public function testDAY()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(DateTime::class, 'DAYOFMONTH'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDAY()
    {
        return require 'data/Calculation/DateTime/DAY.php';
    }

    /**
     * @dataProvider providerTIME
     */
    public function testTIME()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(DateTime::class, 'TIME'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerTIME()
    {
        return require 'data/Calculation/DateTime/TIME.php';
    }

    public function testTIMEtoPHP()
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        Functions::setReturnDateType(Functions::RETURNDATE_PHP_NUMERIC);
        $result = DateTime::TIME(7, 30, 20);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        $this->assertEquals(27020, $result, null, 1E-8);
    }

    public function testTIMEtoPHPObject()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_OBJECT);
        $result = DateTime::TIME(7, 30, 20);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        //    Must return an object...
        $this->assertTrue(is_object($result));
        //    ... of the correct type
        $this->assertTrue(is_a($result, 'DateTime'));
        //    ... with the correct value
        $this->assertEquals($result->format('H:i:s'), '07:30:20');
    }

    /**
     * @dataProvider providerTIMEVALUE
     */
    public function testTIMEVALUE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(DateTime::class, 'TIMEVALUE'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerTIMEVALUE()
    {
        return require 'data/Calculation/DateTime/TIMEVALUE.php';
    }

    public function testTIMEVALUEtoPHP()
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        Functions::setReturnDateType(Functions::RETURNDATE_PHP_NUMERIC);
        $result = DateTime::TIMEVALUE('7:30:20');
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        $this->assertEquals(23420, $result, null, 1E-8);
    }

    public function testTIMEVALUEtoPHPObject()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_OBJECT);
        $result = DateTime::TIMEVALUE('7:30:20');
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        //    Must return an object...
        $this->assertTrue(is_object($result));
        //    ... of the correct type
        $this->assertTrue(is_a($result, 'DateTime'));
        //    ... with the correct value
        $this->assertEquals($result->format('H:i:s'), '07:30:20');
    }

    /**
     * @dataProvider providerHOUR
     */
    public function testHOUR()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(DateTime::class, 'HOUROFDAY'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerHOUR()
    {
        return require 'data/Calculation/DateTime/HOUR.php';
    }

    /**
     * @dataProvider providerMINUTE
     */
    public function testMINUTE()
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(DateTime::class, 'MINUTEOFHOUR'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerMINUTE()
    {
        return require 'data/Calculation/DateTime/MINUTE.php';
    }

    /**
     * @dataProvider providerSECOND
     */
    public function testSECOND()
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(DateTime::class, 'SECONDOFMINUTE'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerSECOND()
    {
        return require 'data/Calculation/DateTime/SECOND.php';
    }

    /**
     * @dataProvider providerNETWORKDAYS
     */
    public function testNETWORKDAYS()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(DateTime::class, 'NETWORKDAYS'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerNETWORKDAYS()
    {
        return require 'data/Calculation/DateTime/NETWORKDAYS.php';
    }

    /**
     * @dataProvider providerWORKDAY
     */
    public function testWORKDAY()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(DateTime::class, 'WORKDAY'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerWORKDAY()
    {
        return require 'data/Calculation/DateTime/WORKDAY.php';
    }

    /**
     * @dataProvider providerEDATE
     */
    public function testEDATE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(DateTime::class, 'EDATE'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
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
        $this->assertEquals(1324857600, $result, null, 1E-8);
    }

    public function testEDATEtoPHPObject()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_OBJECT);
        $result = DateTime::EDATE('2012-1-26', -1);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        //    Must return an object...
        $this->assertTrue(is_object($result));
        //    ... of the correct type
        $this->assertTrue(is_a($result, 'DateTime'));
        //    ... with the correct value
        $this->assertEquals($result->format('d-M-Y'), '26-Dec-2011');
    }

    /**
     * @dataProvider providerEOMONTH
     */
    public function testEOMONTH()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(DateTime::class, 'EOMONTH'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
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
        $this->assertEquals(1325289600, $result, null, 1E-8);
    }

    public function testEOMONTHtoPHPObject()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_OBJECT);
        $result = DateTime::EOMONTH('2012-1-26', -1);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        //    Must return an object...
        $this->assertTrue(is_object($result));
        //    ... of the correct type
        $this->assertTrue(is_a($result, 'DateTime'));
        //    ... with the correct value
        $this->assertEquals($result->format('d-M-Y'), '31-Dec-2011');
    }

    /**
     * @dataProvider providerDATEDIF
     * @group fail19
     */
    public function testDATEDIF()
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(DateTime::class, 'DATEDIF'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDATEDIF()
    {
        return require 'data/Calculation/DateTime/DATEDIF.php';
    }

    /**
     * @dataProvider providerDAYS360
     */
    public function testDAYS360()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(DateTime::class, 'DAYS360'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDAYS360()
    {
        return require 'data/Calculation/DateTime/DAYS360.php';
    }

    /**
     * @dataProvider providerYEARFRAC
     * @group fail19
     */
    public function testYEARFRAC()
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(DateTime::class, 'YEARFRAC'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerYEARFRAC()
    {
        return require 'data/Calculation/DateTime/YEARFRAC.php';
    }
}
