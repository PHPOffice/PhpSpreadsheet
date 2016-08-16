<?php

namespace PhpSpreadsheet\Tests\Calculation;

use PHPExcel\Calculation\Engineering;
use PHPExcel\Calculation\Functions;

class EngineeringTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PhpSpreadsheet\Tests\Custom\ComplexAssert
     */
    protected $complexAssert;

    public function setUp()
    {
        $this->complexAssert = new \PhpSpreadsheet\Tests\Custom\ComplexAssert();
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
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
        $result = call_user_func_array(array(Engineering::class,'BESSELI'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerBESSELI()
    {
        return require 'data/Calculation/Engineering/BESSELI.php';
    }

    /**
     * @dataProvider providerBESSELJ
     */
    public function testBESSELJ()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'BESSELJ'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerBESSELJ()
    {
        return require 'data/Calculation/Engineering/BESSELJ.php';
    }

    /**
     * @dataProvider providerBESSELK
     */
    public function testBESSELK()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'BESSELK'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerBESSELK()
    {
        return require 'data/Calculation/Engineering/BESSELK.php';
    }

    /**
     * @dataProvider providerBESSELY
     */
    public function testBESSELY()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'BESSELY'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerBESSELY()
    {
        return require 'data/Calculation/Engineering/BESSELY.php';
    }

    /**
     * @dataProvider providerCOMPLEX
     */
    public function testCOMPLEX()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'COMPLEX'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCOMPLEX()
    {
        return require 'data/Calculation/Engineering/COMPLEX.php';
    }

    /**
     * @dataProvider providerIMAGINARY
     */
    public function testIMAGINARY()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'IMAGINARY'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIMAGINARY()
    {
        return require 'data/Calculation/Engineering/IMAGINARY.php';
    }

    /**
     * @dataProvider providerIMREAL
     */
    public function testIMREAL()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'IMREAL'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIMREAL()
    {
        return require 'data/Calculation/Engineering/IMREAL.php';
    }

    /**
     * @dataProvider providerIMABS
     */
    public function testIMABS()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'IMABS'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIMABS()
    {
        return require 'data/Calculation/Engineering/IMABS.php';
    }

    /**
     * @dataProvider providerIMARGUMENT
     * @group fail19
     */
    public function testIMARGUMENT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'IMARGUMENT'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIMARGUMENT()
    {
        return require 'data/Calculation/Engineering/IMARGUMENT.php';
    }

    /**
     * @dataProvider providerIMCONJUGATE
     */
    public function testIMCONJUGATE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'IMCONJUGATE'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMCONJUGATE()
    {
        return require 'data/Calculation/Engineering/IMCONJUGATE.php';
    }

    /**
     * @dataProvider providerIMCOS
     */
    public function testIMCOS()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'IMCOS'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMCOS()
    {
        return require 'data/Calculation/Engineering/IMCOS.php';
    }

    /**
     * @dataProvider providerIMDIV
     * @group fail19
     */
    public function testIMDIV()
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'IMDIV'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMDIV()
    {
        return require 'data/Calculation/Engineering/IMDIV.php';
    }

    /**
     * @dataProvider providerIMEXP
     */
    public function testIMEXP()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'IMEXP'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMEXP()
    {
        return require 'data/Calculation/Engineering/IMEXP.php';
    }

    /**
     * @dataProvider providerIMLN
     */
    public function testIMLN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'IMLN'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMLN()
    {
        return require 'data/Calculation/Engineering/IMLN.php';
    }

    /**
     * @dataProvider providerIMLOG2
     */
    public function testIMLOG2()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'IMLOG2'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMLOG2()
    {
        return require 'data/Calculation/Engineering/IMLOG2.php';
    }

    /**
     * @dataProvider providerIMLOG10
     */
    public function testIMLOG10()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'IMLOG10'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMLOG10()
    {
        return require 'data/Calculation/Engineering/IMLOG10.php';
    }

    /**
     * @dataProvider providerIMPOWER
     * @group fail19
     */
    public function testIMPOWER()
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'IMPOWER'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMPOWER()
    {
        return require 'data/Calculation/Engineering/IMPOWER.php';
    }

    /**
     * @dataProvider providerIMPRODUCT
     */
    public function testIMPRODUCT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'IMPRODUCT'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMPRODUCT()
    {
        return require 'data/Calculation/Engineering/IMPRODUCT.php';
    }

    /**
     * @dataProvider providerIMSIN
     */
    public function testIMSIN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'IMSIN'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMSIN()
    {
        return require 'data/Calculation/Engineering/IMSIN.php';
    }

    /**
     * @dataProvider providerIMSQRT
     */
    public function testIMSQRT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'IMSQRT'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMSQRT()
    {
        return require 'data/Calculation/Engineering/IMSQRT.php';
    }

    /**
     * @dataProvider providerIMSUB
     * @group fail19
     */
    public function testIMSUB()
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'IMSUB'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMSUB()
    {
        return require 'data/Calculation/Engineering/IMSUB.php';
    }

    /**
     * @dataProvider providerIMSUM
     * @group fail19
     */
    public function testIMSUM()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'IMSUM'), $args);
        $this->assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMSUM()
    {
        return require 'data/Calculation/Engineering/IMSUM.php';
    }

    /**
     * @dataProvider providerERF
     */
    public function testERF()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'ERF'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerERF()
    {
        return require 'data/Calculation/Engineering/ERF.php';
    }

    /**
     * @dataProvider providerERFC
     */
    public function testERFC()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'ERFC'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerERFC()
    {
        return require 'data/Calculation/Engineering/ERFC.php';
    }

    /**
     * @dataProvider providerBIN2DEC
     */
    public function testBIN2DEC()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'BINTODEC'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerBIN2DEC()
    {
        return require 'data/Calculation/Engineering/BIN2DEC.php';
    }

    /**
     * @dataProvider providerBIN2HEX
     */
    public function testBIN2HEX()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'BINTOHEX'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerBIN2HEX()
    {
        return require 'data/Calculation/Engineering/BIN2HEX.php';
    }

    /**
     * @dataProvider providerBIN2OCT
     */
    public function testBIN2OCT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'BINTOOCT'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerBIN2OCT()
    {
        return require 'data/Calculation/Engineering/BIN2OCT.php';
    }

    /**
     * @dataProvider providerDEC2BIN
     */
    public function testDEC2BIN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'DECTOBIN'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerDEC2BIN()
    {
        return require 'data/Calculation/Engineering/DEC2BIN.php';
    }

    /**
     * @dataProvider providerDEC2HEX
     */
    public function testDEC2HEX()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'DECTOHEX'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerDEC2HEX()
    {
        return require 'data/Calculation/Engineering/DEC2HEX.php';
    }

    /**
     * @dataProvider providerDEC2OCT
     */
    public function testDEC2OCT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'DECTOOCT'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerDEC2OCT()
    {
        return require 'data/Calculation/Engineering/DEC2OCT.php';
    }

    /**
     * @dataProvider providerHEX2BIN
     */
    public function testHEX2BIN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'HEXTOBIN'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerHEX2BIN()
    {
        return require 'data/Calculation/Engineering/HEX2BIN.php';
    }

    /**
     * @dataProvider providerHEX2DEC
     */
    public function testHEX2DEC()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'HEXTODEC'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerHEX2DEC()
    {
        return require 'data/Calculation/Engineering/HEX2DEC.php';
    }

    /**
     * @dataProvider providerHEX2OCT
     */
    public function testHEX2OCT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'HEXTOOCT'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerHEX2OCT()
    {
        return require 'data/Calculation/Engineering/HEX2OCT.php';
    }

    /**
     * @dataProvider providerOCT2BIN
     */
    public function testOCT2BIN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'OCTTOBIN'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerOCT2BIN()
    {
        return require 'data/Calculation/Engineering/OCT2BIN.php';
    }

    /**
     * @dataProvider providerOCT2DEC
     */
    public function testOCT2DEC()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'OCTTODEC'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerOCT2DEC()
    {
        return require 'data/Calculation/Engineering/OCT2DEC.php';
    }

    /**
     * @dataProvider providerOCT2HEX
     */
    public function testOCT2HEX()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'OCTTOHEX'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerOCT2HEX()
    {
        return require 'data/Calculation/Engineering/OCT2HEX.php';
    }

    /**
     * @dataProvider providerDELTA
     */
    public function testDELTA()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'DELTA'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerDELTA()
    {
        return require 'data/Calculation/Engineering/DELTA.php';
    }

    /**
     * @dataProvider providerGESTEP
     */
    public function testGESTEP()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'GESTEP'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerGESTEP()
    {
        return require 'data/Calculation/Engineering/GESTEP.php';
    }

    public function testGetConversionGroups()
    {
        $result = Engineering::getConversionGroups();
        $this->assertInternalType('array', $result);
    }

    public function testGetConversionGroupUnits()
    {
        $result = Engineering::getConversionGroupUnits();
        $this->assertInternalType('array', $result);
    }

    public function testGetConversionGroupUnitDetails()
    {
        $result = Engineering::getConversionGroupUnitDetails();
        $this->assertInternalType('array', $result);
    }

    public function testGetConversionMultipliers()
    {
        $result = Engineering::getConversionMultipliers();
        $this->assertInternalType('array', $result);
    }

    /**
     * @dataProvider providerCONVERTUOM
     */
    public function testCONVERTUOM()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Engineering::class,'CONVERTUOM'), $args);
        $this->assertEquals($expectedResult, $result, null);
    }

    public function providerCONVERTUOM()
    {
        return require 'data/Calculation/Engineering/CONVERTUOM.php';
    }
}
