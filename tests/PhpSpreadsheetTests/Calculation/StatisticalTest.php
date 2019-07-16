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
     * @dataProvider providerBINOMDIST
     *
     * @param mixed $expectedResult
     */
    public function testBINOMDIST($expectedResult, ...$args)
    {
        $result = Statistical::BINOMDIST(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerBINOMDIST()
    {
        return require 'data/Calculation/Statistical/BINOMDIST.php';
    }

    /**
     * @dataProvider providerCHIDIST
     *
     * @param mixed $expectedResult
     */
    public function testCHIDIST($expectedResult, ...$args)
    {
        $result = Statistical::CHIDIST(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerCHIDIST()
    {
        return require 'data/Calculation/Statistical/CHIDIST.php';
    }

    /**
     * @dataProvider providerCHIINV
     *
     * @param mixed $expectedResult
     */
    public function testCHIINV($expectedResult, ...$args)
    {
        $result = Statistical::CHIINV(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerCHIINV()
    {
        return require 'data/Calculation/Statistical/CHIINV.php';
    }

    /**
     * @dataProvider providerCONFIDENCE
     *
     * @param mixed $expectedResult
     */
    public function testCONFIDENCE($expectedResult, ...$args)
    {
        $result = Statistical::CONFIDENCE(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerCONFIDENCE()
    {
        return require 'data/Calculation/Statistical/CONFIDENCE.php';
    }

    /**
     * @dataProvider providerCORREL
     *
     * @param mixed $expectedResult
     */
    public function testCORREL($expectedResult, array $xargs, array $yargs)
    {
        $result = Statistical::CORREL($xargs, $yargs);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerCORREL()
    {
        return require 'data/Calculation/Statistical/CORREL.php';
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
     * @dataProvider providerCOVAR
     *
     * @param mixed $expectedResult
     */
    public function testCOVAR($expectedResult, ...$args)
    {
        $result = Statistical::COVAR(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerCOVAR()
    {
        return require 'data/Calculation/Statistical/COVAR.php';
    }

    /**
     * @dataProvider providerFORECAST
     *
     * @param mixed $expectedResult
     */
    public function testFORECAST($expectedResult, ...$args)
    {
        $result = Statistical::FORECAST(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerFORECAST()
    {
        return require 'data/Calculation/Statistical/FORECAST.php';
    }

    /**
     * @dataProvider providerINTERCEPT
     *
     * @param mixed $expectedResult
     */
    public function testINTERCEPT($expectedResult, array $xargs, array $yargs)
    {
        $result = Statistical::INTERCEPT($xargs, $yargs);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerINTERCEPT()
    {
        return require 'data/Calculation/Statistical/INTERCEPT.php';
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

    /**
     * @dataProvider providerRSQ
     *
     * @param mixed $expectedResult
     */
    public function testRSQ($expectedResult, array $xargs, array $yargs)
    {
        $result = Statistical::RSQ($xargs, $yargs);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerRSQ()
    {
        return require 'data/Calculation/Statistical/RSQ.php';
    }

    /**
     * @dataProvider providerSLOPE
     *
     * @param mixed $expectedResult
     */
    public function testSLOPE($expectedResult, array $xargs, array $yargs)
    {
        $result = Statistical::SLOPE($xargs, $yargs);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerSLOPE()
    {
        return require 'data/Calculation/Statistical/SLOPE.php';
    }

    /**
     * @dataProvider providerSTEYX
     *
     * @param mixed $expectedResult
     */
    public function testSTEYX($expectedResult, array $xargs, array $yargs)
    {
        $result = Statistical::STEYX($xargs, $yargs);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerSTEYX()
    {
        return require 'data/Calculation/Statistical/STEYX.php';
    }
}
