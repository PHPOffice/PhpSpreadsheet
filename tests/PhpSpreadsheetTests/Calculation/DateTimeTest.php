<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
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
}
