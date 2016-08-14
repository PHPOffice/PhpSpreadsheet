<?php

namespace PhpSpreadsheet\Tests\Calculation;

use PHPExcel\Calculation;
use PHPExcel\Calculation\Functions;
use PHPExcel\Calculation\MathTrig;

class MathTrigTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerATAN2
     */
    public function testATAN2()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'ATAN2'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerATAN2()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/ATAN2.data');
    }

    /**
     * @dataProvider providerCEILING
     */
    public function testCEILING()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'CEILING'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerCEILING()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/CEILING.data');
    }

    /**
     * @dataProvider providerCOMBIN
     */
    public function testCOMBIN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'COMBIN'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerCOMBIN()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/COMBIN.data');
    }

    /**
     * @dataProvider providerEVEN
     */
    public function testEVEN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'EVEN'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerEVEN()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/EVEN.data');
    }

    /**
     * @dataProvider providerODD
     */
    public function testODD()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'ODD'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerODD()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/ODD.data');
    }

    /**
     * @dataProvider providerFACT
     */
    public function testFACT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'FACT'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerFACT()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/FACT.data');
    }

    /**
     * @dataProvider providerFACTDOUBLE
     */
    public function testFACTDOUBLE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'FACTDOUBLE'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerFACTDOUBLE()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/FACTDOUBLE.data');
    }

    /**
     * @dataProvider providerFLOOR
     */
    public function testFLOOR()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'FLOOR'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerFLOOR()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/FLOOR.data');
    }

    /**
     * @dataProvider providerGCD
     */
    public function testGCD()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'GCD'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerGCD()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/GCD.data');
    }

    /**
     * @dataProvider providerLCM
     */
    public function testLCM()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'LCM'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerLCM()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/LCM.data');
    }

    /**
     * @dataProvider providerINT
     */
    public function testINT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'INT'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerINT()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/INT.data');
    }

    /**
     * @dataProvider providerSIGN
     */
    public function testSIGN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'SIGN'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSIGN()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/SIGN.data');
    }

    /**
     * @dataProvider providerPOWER
     */
    public function testPOWER()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'POWER'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerPOWER()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/POWER.data');
    }

    /**
     * @dataProvider providerLOG
     */
    public function testLOG()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'logBase'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerLOG()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/LOG.data');
    }

    /**
     * @dataProvider providerMOD
     */
    public function testMOD()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'MOD'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMOD()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/MOD.data');
    }

    /**
     * @dataProvider providerMDETERM
     * @group fail19
     */
    public function testMDETERM()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'MDETERM'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMDETERM()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/MDETERM.data');
    }

    /**
     * @dataProvider providerMINVERSE
     * @group fail19
     */
    public function testMINVERSE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'MINVERSE'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMINVERSE()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/MINVERSE.data');
    }

    /**
     * @dataProvider providerMMULT
     * @group fail19
     */
    public function testMMULT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'MMULT'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMMULT()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/MMULT.data');
    }

    /**
     * @dataProvider providerMULTINOMIAL
     */
    public function testMULTINOMIAL()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'MULTINOMIAL'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMULTINOMIAL()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/MULTINOMIAL.data');
    }

    /**
     * @dataProvider providerMROUND
     */
    public function testMROUND()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_VALUE);
        $result = call_user_func_array(array(MathTrig::class,'MROUND'), $args);
        Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMROUND()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/MROUND.data');
    }

    /**
     * @dataProvider providerPRODUCT
     */
    public function testPRODUCT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'PRODUCT'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerPRODUCT()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/PRODUCT.data');
    }

    /**
     * @dataProvider providerQUOTIENT
     */
    public function testQUOTIENT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'QUOTIENT'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerQUOTIENT()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/QUOTIENT.data');
    }

    /**
     * @dataProvider providerROUNDUP
     */
    public function testROUNDUP()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'ROUNDUP'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerROUNDUP()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/ROUNDUP.data');
    }

    /**
     * @dataProvider providerROUNDDOWN
     */
    public function testROUNDDOWN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'ROUNDDOWN'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerROUNDDOWN()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/ROUNDDOWN.data');
    }

    /**
     * @dataProvider providerSERIESSUM
     */
    public function testSERIESSUM()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'SERIESSUM'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSERIESSUM()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/SERIESSUM.data');
    }

    /**
     * @dataProvider providerSUMSQ
     */
    public function testSUMSQ()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'SUMSQ'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSUMSQ()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/SUMSQ.data');
    }

    /**
     * @dataProvider providerTRUNC
     */
    public function testTRUNC()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'TRUNC'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerTRUNC()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/TRUNC.data');
    }

    /**
     * @dataProvider providerROMAN
     */
    public function testROMAN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'ROMAN'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerROMAN()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/ROMAN.data');
    }

    /**
     * @dataProvider providerSQRTPI
     */
    public function testSQRTPI()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class,'SQRTPI'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSQRTPI()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/MathTrig/SQRTPI.data');
    }

    /**
     * @dataProvider providerSUMIF
     */
    public function testSUMIF()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(MathTrig::class, 'SUMIF'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSUMIF()
    {
        return array(
            array(
                array(
                    array(1),
                    array(5),
                    array(10),
                ),
                '>=5',
                15,
            ),
            array(
                array(
                    array('text'),
                    array(2),
                ),
                '=text',
                array(
                    array(10),
                    array(100),
                ),
                10,
            ),
            array(
                array(
                    array('"text with quotes"'),
                    array(2),
                ),
                '="text with quotes"',
                array(
                    array(10),
                    array(100),
                ),
                10,
            ),
            array(
                array(
                    array('"text with quotes"'),
                    array(''),
                ),
                '>"', // Compare to the single characater " (double quote)
                array(
                    array(10),
                    array(100),
                ),
                10
            ),
            array(
                array(
                    array(''),
                    array('anything'),
                ),
                '>"', // Compare to the single characater " (double quote)
                array(
                    array(10),
                    array(100),
                ),
                100
            ),
        );
    }
}
