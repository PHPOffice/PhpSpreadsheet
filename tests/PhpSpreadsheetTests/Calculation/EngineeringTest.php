<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class EngineeringTest extends TestCase
{
    /**
     * @var ComplexAssert
     */
    protected $complexAssert;

    public function setUp()
    {
        $this->complexAssert = new ComplexAssert();
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    public function tearDown()
    {
        $this->complexAssert = null;
    }

    /**
     * @dataProvider providerBESSELI
     *
     * @param mixed $expectedResult
     */
    public function testBESSELI($expectedResult, ...$args)
    {
        $result = Engineering::BESSELI(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerBESSELI()
    {
        return require 'data/Calculation/Engineering/BESSELI.php';
    }

    /**
     * @dataProvider providerBESSELJ
     *
     * @param mixed $expectedResult
     */
    public function testBESSELJ($expectedResult, ...$args)
    {
        $result = Engineering::BESSELJ(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerBESSELJ()
    {
        return require 'data/Calculation/Engineering/BESSELJ.php';
    }

    /**
     * @dataProvider providerBESSELK
     *
     * @param mixed $expectedResult
     */
    public function testBESSELK($expectedResult, ...$args)
    {
        $result = Engineering::BESSELK(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerBESSELK()
    {
        return require 'data/Calculation/Engineering/BESSELK.php';
    }

    /**
     * @dataProvider providerBESSELY
     *
     * @param mixed $expectedResult
     */
    public function testBESSELY($expectedResult, ...$args)
    {
        $result = Engineering::BESSELY(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerBESSELY()
    {
        return require 'data/Calculation/Engineering/BESSELY.php';
    }

    /**
     * @dataProvider providerCOMPLEX
     *
     * @param mixed $expectedResult
     */
    public function testCOMPLEX($expectedResult, ...$args)
    {
        $result = Engineering::COMPLEX(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCOMPLEX()
    {
        return require 'data/Calculation/Engineering/COMPLEX.php';
    }

    /**
     * @dataProvider providerIMAGINARY
     *
     * @param mixed $expectedResult
     */
    public function testIMAGINARY($expectedResult, ...$args)
    {
        $result = Engineering::IMAGINARY(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIMAGINARY()
    {
        return require 'data/Calculation/Engineering/IMAGINARY.php';
    }

    /**
     * @dataProvider providerIMREAL
     *
     * @param mixed $expectedResult
     */
    public function testIMREAL($expectedResult, ...$args)
    {
        $result = Engineering::IMREAL(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIMREAL()
    {
        return require 'data/Calculation/Engineering/IMREAL.php';
    }

    /**
     * @dataProvider providerIMABS
     *
     * @param mixed $expectedResult
     */
    public function testIMABS($expectedResult, ...$args)
    {
        $result = Engineering::IMABS(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIMABS()
    {
        return require 'data/Calculation/Engineering/IMABS.php';
    }

    /**
     * @dataProvider providerIMARGUMENT
     *
     * @param mixed $expectedResult
     */
    public function testIMARGUMENT($expectedResult, ...$args)
    {
        $result = Engineering::IMARGUMENT(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIMARGUMENT()
    {
        return require 'data/Calculation/Engineering/IMARGUMENT.php';
    }

    /**
     * @dataProvider providerIMCONJUGATE
     *
     * @param mixed $expectedResult
     */
    public function testIMCONJUGATE($expectedResult, ...$args)
    {
        $result = Engineering::IMCONJUGATE(...$args);
        self::assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMCONJUGATE()
    {
        return require 'data/Calculation/Engineering/IMCONJUGATE.php';
    }

    /**
     * @dataProvider providerIMCOS
     *
     * @param mixed $expectedResult
     */
    public function testIMCOS($expectedResult, ...$args)
    {
        $result = Engineering::IMCOS(...$args);
        self::assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMCOS()
    {
        return require 'data/Calculation/Engineering/IMCOS.php';
    }

    /**
     * @dataProvider providerIMDIV
     *
     * @param mixed $expectedResult
     */
    public function testIMDIV($expectedResult, ...$args)
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $result = Engineering::IMDIV(...$args);
        self::assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMDIV()
    {
        return require 'data/Calculation/Engineering/IMDIV.php';
    }

    /**
     * @dataProvider providerIMEXP
     *
     * @param mixed $expectedResult
     */
    public function testIMEXP($expectedResult, ...$args)
    {
        $result = Engineering::IMEXP(...$args);
        self::assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMEXP()
    {
        return require 'data/Calculation/Engineering/IMEXP.php';
    }

    /**
     * @dataProvider providerIMLN
     *
     * @param mixed $expectedResult
     */
    public function testIMLN($expectedResult, ...$args)
    {
        $result = Engineering::IMLN(...$args);
        self::assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMLN()
    {
        return require 'data/Calculation/Engineering/IMLN.php';
    }

    /**
     * @dataProvider providerIMLOG2
     *
     * @param mixed $expectedResult
     */
    public function testIMLOG2($expectedResult, ...$args)
    {
        $result = Engineering::IMLOG2(...$args);
        self::assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMLOG2()
    {
        return require 'data/Calculation/Engineering/IMLOG2.php';
    }

    /**
     * @dataProvider providerIMLOG10
     *
     * @param mixed $expectedResult
     */
    public function testIMLOG10($expectedResult, ...$args)
    {
        $result = Engineering::IMLOG10(...$args);
        self::assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMLOG10()
    {
        return require 'data/Calculation/Engineering/IMLOG10.php';
    }

    /**
     * @dataProvider providerIMPOWER
     *
     * @param mixed $expectedResult
     */
    public function testIMPOWER($expectedResult, ...$args)
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $result = Engineering::IMPOWER(...$args);
        self::assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMPOWER()
    {
        return require 'data/Calculation/Engineering/IMPOWER.php';
    }

    /**
     * @dataProvider providerIMPRODUCT
     *
     * @param mixed $expectedResult
     */
    public function testIMPRODUCT($expectedResult, ...$args)
    {
        $result = Engineering::IMPRODUCT(...$args);
        self::assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMPRODUCT()
    {
        return require 'data/Calculation/Engineering/IMPRODUCT.php';
    }

    /**
     * @dataProvider providerIMSIN
     *
     * @param mixed $expectedResult
     */
    public function testIMSIN($expectedResult, ...$args)
    {
        $result = Engineering::IMSIN(...$args);
        self::assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMSIN()
    {
        return require 'data/Calculation/Engineering/IMSIN.php';
    }

    /**
     * @dataProvider providerIMSQRT
     *
     * @param mixed $expectedResult
     */
    public function testIMSQRT($expectedResult, ...$args)
    {
        $result = Engineering::IMSQRT(...$args);
        self::assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMSQRT()
    {
        return require 'data/Calculation/Engineering/IMSQRT.php';
    }

    /**
     * @dataProvider providerIMSUB
     *
     * @param mixed $expectedResult
     */
    public function testIMSUB($expectedResult, ...$args)
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $result = Engineering::IMSUB(...$args);
        self::assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMSUB()
    {
        return require 'data/Calculation/Engineering/IMSUB.php';
    }

    /**
     * @dataProvider providerIMSUM
     *
     * @param mixed $expectedResult
     */
    public function testIMSUM($expectedResult, ...$args)
    {
        $result = Engineering::IMSUM(...$args);
        self::assertTrue($this->complexAssert->assertComplexEquals($expectedResult, $result, 1E-8), $this->complexAssert->getErrorMessage());
    }

    public function providerIMSUM()
    {
        return require 'data/Calculation/Engineering/IMSUM.php';
    }

    /**
     * @dataProvider providerERF
     *
     * @param mixed $expectedResult
     */
    public function testERF($expectedResult, ...$args)
    {
        $result = Engineering::ERF(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerERF()
    {
        return require 'data/Calculation/Engineering/ERF.php';
    }

    /**
     * @dataProvider providerERFC
     *
     * @param mixed $expectedResult
     */
    public function testERFC($expectedResult, ...$args)
    {
        $result = Engineering::ERFC(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerERFC()
    {
        return require 'data/Calculation/Engineering/ERFC.php';
    }

    /**
     * @dataProvider providerBIN2DEC
     *
     * @param mixed $expectedResult
     */
    public function testBIN2DEC($expectedResult, ...$args)
    {
        $result = Engineering::BINTODEC(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerBIN2DEC()
    {
        return require 'data/Calculation/Engineering/BIN2DEC.php';
    }

    /**
     * @dataProvider providerBIN2HEX
     *
     * @param mixed $expectedResult
     */
    public function testBIN2HEX($expectedResult, ...$args)
    {
        $result = Engineering::BINTOHEX(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerBIN2HEX()
    {
        return require 'data/Calculation/Engineering/BIN2HEX.php';
    }

    /**
     * @dataProvider providerBIN2OCT
     *
     * @param mixed $expectedResult
     */
    public function testBIN2OCT($expectedResult, ...$args)
    {
        $result = Engineering::BINTOOCT(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerBIN2OCT()
    {
        return require 'data/Calculation/Engineering/BIN2OCT.php';
    }

    /**
     * @dataProvider providerDEC2BIN
     *
     * @param mixed $expectedResult
     */
    public function testDEC2BIN($expectedResult, ...$args)
    {
        $result = Engineering::DECTOBIN(...$args);
        self::assertEquals($expectedResult, $result, null);
    }

    public function providerDEC2BIN()
    {
        return require 'data/Calculation/Engineering/DEC2BIN.php';
    }

    /**
     * @dataProvider providerDEC2HEX
     *
     * @param mixed $expectedResult
     */
    public function testDEC2HEX($expectedResult, ...$args)
    {
        $result = Engineering::DECTOHEX(...$args);
        self::assertEquals($expectedResult, $result, null);
    }

    public function providerDEC2HEX()
    {
        return require 'data/Calculation/Engineering/DEC2HEX.php';
    }

    /**
     * @dataProvider providerDEC2OCT
     *
     * @param mixed $expectedResult
     */
    public function testDEC2OCT($expectedResult, ...$args)
    {
        $result = Engineering::DECTOOCT(...$args);
        self::assertEquals($expectedResult, $result, null);
    }

    public function providerDEC2OCT()
    {
        return require 'data/Calculation/Engineering/DEC2OCT.php';
    }

    /**
     * @dataProvider providerHEX2BIN
     *
     * @param mixed $expectedResult
     */
    public function testHEX2BIN($expectedResult, ...$args)
    {
        $result = Engineering::HEXTOBIN(...$args);
        self::assertEquals($expectedResult, $result, null);
    }

    public function providerHEX2BIN()
    {
        return require 'data/Calculation/Engineering/HEX2BIN.php';
    }

    /**
     * @dataProvider providerHEX2DEC
     *
     * @param mixed $expectedResult
     */
    public function testHEX2DEC($expectedResult, ...$args)
    {
        $result = Engineering::HEXTODEC(...$args);
        self::assertEquals($expectedResult, $result, null);
    }

    public function providerHEX2DEC()
    {
        return require 'data/Calculation/Engineering/HEX2DEC.php';
    }

    /**
     * @dataProvider providerHEX2OCT
     *
     * @param mixed $expectedResult
     */
    public function testHEX2OCT($expectedResult, ...$args)
    {
        $result = Engineering::HEXTOOCT(...$args);
        self::assertEquals($expectedResult, $result, null);
    }

    public function providerHEX2OCT()
    {
        return require 'data/Calculation/Engineering/HEX2OCT.php';
    }

    /**
     * @dataProvider providerOCT2BIN
     *
     * @param mixed $expectedResult
     */
    public function testOCT2BIN($expectedResult, ...$args)
    {
        $result = Engineering::OCTTOBIN(...$args);
        self::assertEquals($expectedResult, $result, null);
    }

    public function providerOCT2BIN()
    {
        return require 'data/Calculation/Engineering/OCT2BIN.php';
    }

    /**
     * @dataProvider providerOCT2DEC
     *
     * @param mixed $expectedResult
     */
    public function testOCT2DEC($expectedResult, ...$args)
    {
        $result = Engineering::OCTTODEC(...$args);
        self::assertEquals($expectedResult, $result, null);
    }

    public function providerOCT2DEC()
    {
        return require 'data/Calculation/Engineering/OCT2DEC.php';
    }

    /**
     * @dataProvider providerOCT2HEX
     *
     * @param mixed $expectedResult
     */
    public function testOCT2HEX($expectedResult, ...$args)
    {
        $result = Engineering::OCTTOHEX(...$args);
        self::assertEquals($expectedResult, $result, null);
    }

    public function providerOCT2HEX()
    {
        return require 'data/Calculation/Engineering/OCT2HEX.php';
    }

    /**
     * @dataProvider providerDELTA
     *
     * @param mixed $expectedResult
     */
    public function testDELTA($expectedResult, ...$args)
    {
        $result = Engineering::DELTA(...$args);
        self::assertEquals($expectedResult, $result, null);
    }

    public function providerDELTA()
    {
        return require 'data/Calculation/Engineering/DELTA.php';
    }

    /**
     * @dataProvider providerGESTEP
     *
     * @param mixed $expectedResult
     */
    public function testGESTEP($expectedResult, ...$args)
    {
        $result = Engineering::GESTEP(...$args);
        self::assertEquals($expectedResult, $result, null);
    }

    public function providerGESTEP()
    {
        return require 'data/Calculation/Engineering/GESTEP.php';
    }

    public function testGetConversionGroups()
    {
        $result = Engineering::getConversionGroups();
        self::assertInternalType('array', $result);
    }

    public function testGetConversionGroupUnits()
    {
        $result = Engineering::getConversionGroupUnits();
        self::assertInternalType('array', $result);
    }

    public function testGetConversionGroupUnitDetails()
    {
        $result = Engineering::getConversionGroupUnitDetails();
        self::assertInternalType('array', $result);
    }

    public function testGetConversionMultipliers()
    {
        $result = Engineering::getConversionMultipliers();
        self::assertInternalType('array', $result);
    }

    /**
     * @dataProvider providerCONVERTUOM
     *
     * @param mixed $expectedResult
     */
    public function testCONVERTUOM($expectedResult, ...$args)
    {
        $result = Engineering::CONVERTUOM(...$args);
        self::assertEquals($expectedResult, $result, null);
    }

    public function providerCONVERTUOM()
    {
        return require 'data/Calculation/Engineering/CONVERTUOM.php';
    }
}
