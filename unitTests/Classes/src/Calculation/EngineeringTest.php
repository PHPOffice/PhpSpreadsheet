<?php

namespace PHPExcel\Calculation;

//  Custom assertion class for handling precision of Complex numbers
require_once 'custom/complexAssert.php';

//  Data Provider handler
require_once 'testDataFileIterator.php';


class EngineeringTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PhpSpreadhsheet\unitTests\custom\ComplexAssert
     */
    protected $complexAssert;

    public function setUp()
    {
        $this->complexAssert = new \PhpSpreadhsheet\unitTests\custom\ComplexAssert();
        \PHPExcel\Calculation\Functions::setCompatibilityMode(\PHPExcel\Calculation\Functions::COMPATIBILITY_EXCEL);
    }

    public function tearDown()
    {
        $this->complexAssert = null;
    }

    /**
     * @dataProvider providerBESSELI
     */
    public function testBESSELI()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','BESSELI'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerBESSELI()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/BESSELI.data');
    }

    /**
     * @dataProvider providerBESSELJ
     */
    public function testBESSELJ()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','BESSELJ'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerBESSELJ()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/BESSELJ.data');
    }

    /**
     * @dataProvider providerBESSELK
     */
    public function testBESSELK()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','BESSELK'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerBESSELK()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/BESSELK.data');
    }

    /**
     * @dataProvider providerBESSELY
     */
    public function testBESSELY()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','BESSELY'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerBESSELY()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/BESSELY.data');
    }

    /**
     * @dataProvider providerCOMPLEX
     */
    public function testCOMPLEX()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','COMPLEX'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCOMPLEX()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/COMPLEX.data');
    }

    /**
     * @dataProvider providerIMAGINARY
     */
    public function testIMAGINARY()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','IMAGINARY'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIMAGINARY()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/IMAGINARY.data');
    }

    /**
     * @dataProvider providerIMREAL
     */
    public function testIMREAL()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','IMREAL'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIMREAL()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/IMREAL.data');
    }

    /**
     * @dataProvider providerIMABS
     */
    public function testIMABS()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','IMABS'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIMABS()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/IMABS.data');
    }

    /**
     * @dataProvider providerIMARGUMENT
     * @group fail19
     */
    public function testIMARGUMENT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','IMARGUMENT'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIMARGUMENT()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/IMARGUMENT.data');
    }

    /**
     * @dataProvider providerIMCONJUGATE
     */
    public function testIMCONJUGATE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','IMCONJUGATE'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMCONJUGATE()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/IMCONJUGATE.data');
    }

    /**
     * @dataProvider providerIMCOS
     */
    public function testIMCOS()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','IMCOS'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMCOS()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/IMCOS.data');
    }

    /**
     * @dataProvider providerIMDIV
     * @group fail19
     */
    public function testIMDIV()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','IMDIV'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMDIV()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/IMDIV.data');
    }

    /**
     * @dataProvider providerIMEXP
     * @group fail19
     */
    public function testIMEXP()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','IMEXP'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMEXP()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/IMEXP.data');
    }

    /**
     * @dataProvider providerIMLN
     */
    public function testIMLN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','IMLN'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMLN()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/IMLN.data');
    }

    /**
     * @dataProvider providerIMLOG2
     */
    public function testIMLOG2()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','IMLOG2'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMLOG2()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/IMLOG2.data');
    }

    /**
     * @dataProvider providerIMLOG10
     */
    public function testIMLOG10()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','IMLOG10'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMLOG10()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/IMLOG10.data');
    }

    /**
     * @dataProvider providerIMPOWER
     * @group fail19
     */
    public function testIMPOWER()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','IMPOWER'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMPOWER()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/IMPOWER.data');
    }

    /**
     * @dataProvider providerIMPRODUCT
     */
    public function testIMPRODUCT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','IMPRODUCT'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMPRODUCT()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/IMPRODUCT.data');
    }

    /**
     * @dataProvider providerIMSIN
     */
    public function testIMSIN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','IMSIN'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMSIN()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/IMSIN.data');
    }

    /**
     * @dataProvider providerIMSQRT
     */
    public function testIMSQRT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','IMSQRT'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMSQRT()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/IMSQRT.data');
    }

    /**
     * @dataProvider providerIMSUB
     * @group fail19
     */
    public function testIMSUB()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','IMSUB'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMSUB()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/IMSUB.data');
    }

    /**
     * @dataProvider providerIMSUM
     * @group fail19
     */
    public function testIMSUM()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','IMSUM'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMSUM()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/IMSUM.data');
    }

    /**
     * @dataProvider providerERF
     */
    public function testERF()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','ERF'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerERF()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/ERF.data');
    }

    /**
     * @dataProvider providerERFC
     */
    public function testERFC()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','ERFC'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerERFC()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/ERFC.data');
    }

    /**
     * @dataProvider providerBIN2DEC
     */
    public function testBIN2DEC()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','BINTODEC'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerBIN2DEC()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/BIN2DEC.data');
    }

    /**
     * @dataProvider providerBIN2HEX
     */
    public function testBIN2HEX()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','BINTOHEX'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerBIN2HEX()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/BIN2HEX.data');
    }

    /**
     * @dataProvider providerBIN2OCT
     */
    public function testBIN2OCT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','BINTOOCT'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerBIN2OCT()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/BIN2OCT.data');
    }

    /**
     * @dataProvider providerDEC2BIN
     * @group fail19
     */
    public function testDEC2BIN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','DECTOBIN'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerDEC2BIN()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/DEC2BIN.data');
    }

    /**
     * @dataProvider providerDEC2HEX
     */
    public function testDEC2HEX()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','DECTOHEX'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerDEC2HEX()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/DEC2HEX.data');
    }

    /**
     * @dataProvider providerDEC2OCT
     */
    public function testDEC2OCT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','DECTOOCT'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerDEC2OCT()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/DEC2OCT.data');
    }

    /**
     * @dataProvider providerHEX2BIN
     * @group fail19
     */
    public function testHEX2BIN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','HEXTOBIN'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerHEX2BIN()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/HEX2BIN.data');
    }

    /**
     * @dataProvider providerHEX2DEC
     * @group fail19
     */
    public function testHEX2DEC()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','HEXTODEC'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerHEX2DEC()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/HEX2DEC.data');
    }

    /**
     * @dataProvider providerHEX2OCT
     * @group fail19
     */
    public function testHEX2OCT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','HEXTOOCT'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerHEX2OCT()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/HEX2OCT.data');
    }

    /**
     * @dataProvider providerOCT2BIN
     * @group fail19
     */
    public function testOCT2BIN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','OCTTOBIN'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerOCT2BIN()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/OCT2BIN.data');
    }

    /**
     * @dataProvider providerOCT2DEC
     */
    public function testOCT2DEC()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','OCTTODEC'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerOCT2DEC()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/OCT2DEC.data');
    }

    /**
     * @dataProvider providerOCT2HEX
     */
    public function testOCT2HEX()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','OCTTOHEX'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerOCT2HEX()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/OCT2HEX.data');
    }

    /**
     * @dataProvider providerDELTA
     */
    public function testDELTA()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','DELTA'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerDELTA()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/DELTA.data');
    }

    /**
     * @dataProvider providerGESTEP
     */
    public function testGESTEP()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','GESTEP'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerGESTEP()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/GESTEP.data');
    }

    public function testGetConversionGroups()
    {
        $result = \PHPExcel\Calculation\Engineering::getConversionGroups();
        $this->assertInternalType('array', $result);
    }

    public function testGetConversionGroupUnits()
    {
        $result = \PHPExcel\Calculation\Engineering::getConversionGroupUnits();
        $this->assertInternalType('array', $result);
    }

    public function testGetConversionGroupUnitDetails()
    {
        $result = \PHPExcel\Calculation\Engineering::getConversionGroupUnitDetails();
        $this->assertInternalType('array', $result);
    }

    public function testGetConversionMultipliers()
    {
        $result = \PHPExcel\Calculation\Engineering::getConversionMultipliers();
        $this->assertInternalType('array', $result);
    }

    /**
     * @dataProvider providerCONVERTUOM
     */
    public function testCONVERTUOM()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Engineering','CONVERTUOM'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerCONVERTUOM()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/Engineering/CONVERTUOM.data');
    }
}
