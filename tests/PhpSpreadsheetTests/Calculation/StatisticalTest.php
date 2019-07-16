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
     * @dataProvider providerAVEDEV
     *
     * @param mixed $expectedResult
     */
    public function testAVEDEV($expectedResult, ...$args)
    {
        $result = Statistical::AVEDEV(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerAVEDEV()
    {
        return require 'data/Calculation/Statistical/AVEDEV.php';
    }

    /**
     * @dataProvider providerAVERAGE
     *
     * @param mixed $expectedResult
     */
    public function testAVERAGE($expectedResult, ...$args)
    {
        $result = Statistical::AVERAGE(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerAVERAGE()
    {
        return require 'data/Calculation/Statistical/AVERAGE.php';
    }

    /**
     * @dataProvider providerAVERAGEA
     *
     * @param mixed $expectedResult
     */
    public function testAVERAGEA($expectedResult, ...$args)
    {
        $result = Statistical::AVERAGEA(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerAVERAGEA()
    {
        return require 'data/Calculation/Statistical/AVERAGEA.php';
    }

    /**
     * @dataProvider providerAVERAGEIF
     *
     * @param mixed $expectedResult
     */
    public function testAVERAGEIF($expectedResult, ...$args)
    {
        $result = Statistical::AVERAGEIF(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerAVERAGEIF()
    {
        return require 'data/Calculation/Statistical/AVERAGEIF.php';
    }

    /**
     * @dataProvider providerBETADIST
     *
     * @param mixed $expectedResult
     */
    public function testBETADIST($expectedResult, ...$args)
    {
        $result = Statistical::BETADIST(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerBETADIST()
    {
        return require 'data/Calculation/Statistical/BETADIST.php';
    }

    /**
     * @dataProvider providerBETAINV
     *
     * @param mixed $expectedResult
     */
    public function testBETAINV($expectedResult, ...$args)
    {
        $result = Statistical::BETAINV(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerBETAINV()
    {
        return require 'data/Calculation/Statistical/BETAINV.php';
    }

    /**
     * @dataProvider providerCOUNTIF
     *
     * @param mixed $expectedResult
     */
    public function testCOUNTIF($expectedResult, ...$args)
    {
        $result = Statistical::COUNTIF(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerCOUNTIF()
    {
        return require 'data/Calculation/Statistical/COUNTIF.php';
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
