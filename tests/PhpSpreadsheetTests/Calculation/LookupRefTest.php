<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit_Framework_TestCase;

/**
 * Class LookupRefTest.
 */
class LookupRefTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerHLOOKUP
     *
     * @param mixed $expectedResult
     */
    public function testHLOOKUP($expectedResult, ...$args)
    {
        $result = LookupRef::HLOOKUP(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerHLOOKUP()
    {
        return require 'data/Calculation/LookupRef/HLOOKUP.php';
    }

    /**
     * @dataProvider providerVLOOKUP
     *
     * @param mixed $expectedResult
     */
    public function testVLOOKUP($expectedResult, ...$args)
    {
        $result = LookupRef::VLOOKUP(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerVLOOKUP()
    {
        return require 'data/Calculation/LookupRef/VLOOKUP.php';
    }

    /**
     * @dataProvider providerMATCH
     *
     * @param mixed $expectedResult
     */
    public function testMATCH($expectedResult, ...$args)
    {
        $result = LookupRef::MATCH(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerMATCH()
    {
        return require 'data/Calculation/LookupRef/MATCH.php';
    }

    /**
     * @dataProvider providerINDEX
     *
     * @param mixed $expectedResult
     */
    public function testINDEX($expectedResult, ...$args)
    {
        $result = LookupRef::INDEX(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerINDEX()
    {
        return require 'data/Calculation/LookupRef/INDEX.php';
    }
}
