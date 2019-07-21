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
     * @dataProvider providerDATEVALUE
     *
     * @param mixed $expectedResult
     */
    public function testDATEVALUE($expectedResult, ...$args)
    {
        $result = DateTime::DATEVALUE(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-8);
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
        self::assertEquals(1327968000, $result, '', 1E-8);
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
     * @dataProvider providerTIMEVALUE
     *
     * @param mixed $expectedResult
     */
    public function testTIMEVALUE($expectedResult, ...$args)
    {
        $result = DateTime::TIMEVALUE(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-8);
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
        self::assertEquals(23420, $result, '', 1E-8);
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
     * @dataProvider providerNETWORKDAYS
     *
     * @param mixed $expectedResult
     */
    public function testNETWORKDAYS($expectedResult, ...$args)
    {
        $result = DateTime::NETWORKDAYS(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-8);
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
        self::assertEquals($expectedResult, $result, '', 1E-8);
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
        self::assertEquals($expectedResult, $result, '', 1E-8);
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
        self::assertEquals(1324857600, $result, '', 1E-8);
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
        self::assertEquals($expectedResult, $result, '', 1E-8);
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
        self::assertEquals(1325289600, $result, '', 1E-8);
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
        self::assertEquals($expectedResult, $result, '', 1E-8);
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
        self::assertEquals($expectedResult, $result, '', 1E-8);
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
        self::assertEquals($expectedResult, $result, '', 1E-8);
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
        self::assertEquals($expectedResult, $result, '', 1E-8);
    }

    public function providerYEARFRAC()
    {
        return require 'data/Calculation/DateTime/YEARFRAC.php';
    }
}
