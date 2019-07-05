<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class StatisticalTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCOUNTIFS
     *
     * @param mixed $expectedResult
     */
    public function testCOUNTIFS($expectedResult, ...$args)
    {
        $result = Statistical::COUNTIFS(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerCOUNTIFS()
    {
        return require 'data/Calculation/Statistical/COUNTIFS.php';
    }

    /**
     * @dataProvider providerMAXIFS
     *
     * @param mixed $expectedResult
     */
    public function testMAXIFS($expectedResult, ...$args)
    {
        $result = Statistical::MAXIFS(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerMAXIFS()
    {
        return require 'data/Calculation/Statistical/MAXIFS.php';
    }

    /**
     * @dataProvider providerMINIFS
     *
     * @param mixed $expectedResult
     */
    public function testMINIFS($expectedResult, ...$args)
    {
        $result = Statistical::MINIFS(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerMINIFS()
    {
        return require 'data/Calculation/Statistical/MINIFS.php';
    }
}
