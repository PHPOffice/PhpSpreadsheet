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

    const COMPLEX_PRECISION = 1E-8;

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
     * @dataProvider providerCOMPLEX
     *
     * @param mixed $expectedResult
     */
    public function testParseComplex()
    {
        list($real, $imaginary, $suffix) = [1.23e-4, 5.67e+8, 'j'];

        $result = Engineering::parseComplex('1.23e-4+5.67e+8j');
        $this->assertArrayHasKey('real', $result);
        $this->assertEquals($real, $result['real']);
        $this->assertArrayHasKey('imaginary', $result);
        $this->assertEquals($imaginary, $result['imaginary']);
        $this->assertArrayHasKey('suffix', $result);
        $this->assertEquals($suffix, $result['suffix']);
    }

    /**
     * @dataProvider providerIMCOS
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMCOS($expectedResult, $value)
    {
        $result = Engineering::IMCOS($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMCOS()
    {
        return require 'data/Calculation/Engineering/IMCOS.php';
    }

    /**
     * @dataProvider providerIMCOSH
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMCOSH($expectedResult, $value)
    {
        $result = Engineering::IMCOSH($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMCOSH()
    {
        return require 'data/Calculation/Engineering/IMCOSH.php';
    }

    /**
     * @dataProvider providerIMCOT
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMCOT($expectedResult, $value)
    {
        $result = Engineering::IMCOT($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMCOT()
    {
        return require 'data/Calculation/Engineering/IMCOT.php';
    }

    /**
     * @dataProvider providerIMCSC
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMCSC($expectedResult, $value)
    {
        $result = Engineering::IMCSC($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMCSC()
    {
        return require 'data/Calculation/Engineering/IMCSC.php';
    }

    /**
     * @dataProvider providerIMCSCH
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMCSCH($expectedResult, $value)
    {
        $result = Engineering::IMCSCH($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMCSCH()
    {
        return require 'data/Calculation/Engineering/IMCSCH.php';
    }

    /**
     * @dataProvider providerIMSEC
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMSEC($expectedResult, $value)
    {
        $result = Engineering::IMSEC($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMSEC()
    {
        return require 'data/Calculation/Engineering/IMSEC.php';
    }

    /**
     * @dataProvider providerIMSECH
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMSECH($expectedResult, $value)
    {
        $result = Engineering::IMSECH($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMSECH()
    {
        return require 'data/Calculation/Engineering/IMSECH.php';
    }

    /**
     * @dataProvider providerIMDIV
     *
     * @param mixed $expectedResult
     */
    public function testIMDIV($expectedResult, ...$args)
    {
        $result = Engineering::IMDIV(...$args);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMDIV()
    {
        return require 'data/Calculation/Engineering/IMDIV.php';
    }

    /**
     * @dataProvider providerIMEXP
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMEXP($expectedResult, $value)
    {
        $result = Engineering::IMEXP($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMEXP()
    {
        return require 'data/Calculation/Engineering/IMEXP.php';
    }

    /**
     * @dataProvider providerIMLN
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMLN($expectedResult, $value)
    {
        $result = Engineering::IMLN($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMLN()
    {
        return require 'data/Calculation/Engineering/IMLN.php';
    }

    /**
     * @dataProvider providerIMLOG2
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMLOG2($expectedResult, $value)
    {
        $result = Engineering::IMLOG2($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMLOG2()
    {
        return require 'data/Calculation/Engineering/IMLOG2.php';
    }

    /**
     * @dataProvider providerIMLOG10
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMLOG10($expectedResult, $value)
    {
        $result = Engineering::IMLOG10($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
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
        $result = Engineering::IMPOWER(...$args);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
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
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMPRODUCT()
    {
        return require 'data/Calculation/Engineering/IMPRODUCT.php';
    }

    /**
     * @dataProvider providerIMSIN
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMSIN($expectedResult, $value)
    {
        $result = Engineering::IMSIN($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMSIN()
    {
        return require 'data/Calculation/Engineering/IMSIN.php';
    }

    /**
     * @dataProvider providerIMSINH
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMSINH($expectedResult, $value)
    {
        $result = Engineering::IMSINH($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMSINH()
    {
        return require 'data/Calculation/Engineering/IMSINH.php';
    }

    /**
     * @dataProvider providerIMTAN
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMTAN($expectedResult, $value)
    {
        $result = Engineering::IMTAN($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMTAN()
    {
        return require 'data/Calculation/Engineering/IMTAN.php';
    }

    /**
     * @dataProvider providerIMSQRT
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMSQRT($expectedResult, $value)
    {
        $result = Engineering::IMSQRT($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
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
        $result = Engineering::IMSUB(...$args);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
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
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMSUM()
    {
        return require 'data/Calculation/Engineering/IMSUM.php';
    }
}
