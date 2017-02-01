<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class MathTrigTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerATAN2
     *
     * @param mixed $expectedResult
     */
    public function testATAN2($expectedResult, ...$args)
    {
        $result = MathTrig::ATAN2(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerATAN2()
    {
        return require 'data/Calculation/MathTrig/ATAN2.php';
    }

    /**
     * @dataProvider providerCEILING
     *
     * @param mixed $expectedResult
     */
    public function testCEILING($expectedResult, ...$args)
    {
        $result = MathTrig::CEILING(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerCEILING()
    {
        return require 'data/Calculation/MathTrig/CEILING.php';
    }

    /**
     * @dataProvider providerCOMBIN
     *
     * @param mixed $expectedResult
     */
    public function testCOMBIN($expectedResult, ...$args)
    {
        $result = MathTrig::COMBIN(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerCOMBIN()
    {
        return require 'data/Calculation/MathTrig/COMBIN.php';
    }

    /**
     * @dataProvider providerEVEN
     *
     * @param mixed $expectedResult
     */
    public function testEVEN($expectedResult, ...$args)
    {
        $result = MathTrig::EVEN(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerEVEN()
    {
        return require 'data/Calculation/MathTrig/EVEN.php';
    }

    /**
     * @dataProvider providerODD
     *
     * @param mixed $expectedResult
     */
    public function testODD($expectedResult, ...$args)
    {
        $result = MathTrig::ODD(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerODD()
    {
        return require 'data/Calculation/MathTrig/ODD.php';
    }

    /**
     * @dataProvider providerFACT
     *
     * @param mixed $expectedResult
     */
    public function testFACT($expectedResult, ...$args)
    {
        $result = MathTrig::FACT(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerFACT()
    {
        return require 'data/Calculation/MathTrig/FACT.php';
    }

    /**
     * @dataProvider providerFACTDOUBLE
     *
     * @param mixed $expectedResult
     */
    public function testFACTDOUBLE($expectedResult, ...$args)
    {
        $result = MathTrig::FACTDOUBLE(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerFACTDOUBLE()
    {
        return require 'data/Calculation/MathTrig/FACTDOUBLE.php';
    }

    /**
     * @dataProvider providerFLOOR
     *
     * @param mixed $expectedResult
     */
    public function testFLOOR($expectedResult, ...$args)
    {
        $result = MathTrig::FLOOR(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerFLOOR()
    {
        return require 'data/Calculation/MathTrig/FLOOR.php';
    }

    /**
     * @dataProvider providerGCD
     *
     * @param mixed $expectedResult
     */
    public function testGCD($expectedResult, ...$args)
    {
        $result = MathTrig::GCD(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerGCD()
    {
        return require 'data/Calculation/MathTrig/GCD.php';
    }

    /**
     * @dataProvider providerLCM
     *
     * @param mixed $expectedResult
     */
    public function testLCM($expectedResult, ...$args)
    {
        $result = MathTrig::LCM(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerLCM()
    {
        return require 'data/Calculation/MathTrig/LCM.php';
    }

    /**
     * @dataProvider providerINT
     *
     * @param mixed $expectedResult
     */
    public function testINT($expectedResult, ...$args)
    {
        $result = MathTrig::INT(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerINT()
    {
        return require 'data/Calculation/MathTrig/INT.php';
    }

    /**
     * @dataProvider providerSIGN
     *
     * @param mixed $expectedResult
     */
    public function testSIGN($expectedResult, ...$args)
    {
        $result = MathTrig::SIGN(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSIGN()
    {
        return require 'data/Calculation/MathTrig/SIGN.php';
    }

    /**
     * @dataProvider providerPOWER
     *
     * @param mixed $expectedResult
     */
    public function testPOWER($expectedResult, ...$args)
    {
        $result = MathTrig::POWER(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerPOWER()
    {
        return require 'data/Calculation/MathTrig/POWER.php';
    }

    /**
     * @dataProvider providerLOG
     *
     * @param mixed $expectedResult
     */
    public function testLOG($expectedResult, ...$args)
    {
        $result = MathTrig::logBase(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerLOG()
    {
        return require 'data/Calculation/MathTrig/LOG.php';
    }

    /**
     * @dataProvider providerMOD
     *
     * @param mixed $expectedResult
     */
    public function testMOD($expectedResult, ...$args)
    {
        $result = MathTrig::MOD(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMOD()
    {
        return require 'data/Calculation/MathTrig/MOD.php';
    }

    /**
     * @dataProvider providerMDETERM
     *
     * @param mixed $expectedResult
     */
    public function testMDETERM($expectedResult, ...$args)
    {
        $result = MathTrig::MDETERM(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMDETERM()
    {
        return require 'data/Calculation/MathTrig/MDETERM.php';
    }

    /**
     * @dataProvider providerMINVERSE
     * @group fail19
     *
     * @param mixed $expectedResult
     */
    public function testMINVERSE($expectedResult, ...$args)
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $result = MathTrig::MINVERSE(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMINVERSE()
    {
        return require 'data/Calculation/MathTrig/MINVERSE.php';
    }

    /**
     * @dataProvider providerMMULT
     * @group fail19
     *
     * @param mixed $expectedResult
     */
    public function testMMULT($expectedResult, ...$args)
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $result = MathTrig::MMULT(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMMULT()
    {
        return require 'data/Calculation/MathTrig/MMULT.php';
    }

    /**
     * @dataProvider providerMULTINOMIAL
     *
     * @param mixed $expectedResult
     */
    public function testMULTINOMIAL($expectedResult, ...$args)
    {
        $result = MathTrig::MULTINOMIAL(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMULTINOMIAL()
    {
        return require 'data/Calculation/MathTrig/MULTINOMIAL.php';
    }

    /**
     * @dataProvider providerMROUND
     *
     * @param mixed $expectedResult
     */
    public function testMROUND($expectedResult, ...$args)
    {
        Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_VALUE);
        $result = MathTrig::MROUND(...$args);
        Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMROUND()
    {
        return require 'data/Calculation/MathTrig/MROUND.php';
    }

    /**
     * @dataProvider providerPRODUCT
     *
     * @param mixed $expectedResult
     */
    public function testPRODUCT($expectedResult, ...$args)
    {
        $result = MathTrig::PRODUCT(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerPRODUCT()
    {
        return require 'data/Calculation/MathTrig/PRODUCT.php';
    }

    /**
     * @dataProvider providerQUOTIENT
     *
     * @param mixed $expectedResult
     */
    public function testQUOTIENT($expectedResult, ...$args)
    {
        $result = MathTrig::QUOTIENT(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerQUOTIENT()
    {
        return require 'data/Calculation/MathTrig/QUOTIENT.php';
    }

    /**
     * @dataProvider providerROUNDUP
     *
     * @param mixed $expectedResult
     */
    public function testROUNDUP($expectedResult, ...$args)
    {
        $result = MathTrig::ROUNDUP(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerROUNDUP()
    {
        return require 'data/Calculation/MathTrig/ROUNDUP.php';
    }

    /**
     * @dataProvider providerROUNDDOWN
     *
     * @param mixed $expectedResult
     */
    public function testROUNDDOWN($expectedResult, ...$args)
    {
        $result = MathTrig::ROUNDDOWN(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerROUNDDOWN()
    {
        return require 'data/Calculation/MathTrig/ROUNDDOWN.php';
    }

    /**
     * @dataProvider providerSERIESSUM
     *
     * @param mixed $expectedResult
     */
    public function testSERIESSUM($expectedResult, ...$args)
    {
        $result = MathTrig::SERIESSUM(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSERIESSUM()
    {
        return require 'data/Calculation/MathTrig/SERIESSUM.php';
    }

    /**
     * @dataProvider providerSUMSQ
     *
     * @param mixed $expectedResult
     */
    public function testSUMSQ($expectedResult, ...$args)
    {
        $result = MathTrig::SUMSQ(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSUMSQ()
    {
        return require 'data/Calculation/MathTrig/SUMSQ.php';
    }

    /**
     * @dataProvider providerTRUNC
     *
     * @param mixed $expectedResult
     */
    public function testTRUNC($expectedResult, ...$args)
    {
        $result = MathTrig::TRUNC(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerTRUNC()
    {
        return require 'data/Calculation/MathTrig/TRUNC.php';
    }

    /**
     * @dataProvider providerROMAN
     *
     * @param mixed $expectedResult
     */
    public function testROMAN($expectedResult, ...$args)
    {
        $result = MathTrig::ROMAN(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerROMAN()
    {
        return require 'data/Calculation/MathTrig/ROMAN.php';
    }

    /**
     * @dataProvider providerSQRTPI
     *
     * @param mixed $expectedResult
     */
    public function testSQRTPI($expectedResult, ...$args)
    {
        $result = MathTrig::SQRTPI(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSQRTPI()
    {
        return require 'data/Calculation/MathTrig/SQRTPI.php';
    }

    /**
     * @dataProvider providerSUMIF
     *
     * @param mixed $expectedResult
     */
    public function testSUMIF($expectedResult, ...$args)
    {
        $result = MathTrig::SUMIF(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSUMIF()
    {
        return require 'data/Calculation/MathTrig/SUMIF.php';
    }
}
