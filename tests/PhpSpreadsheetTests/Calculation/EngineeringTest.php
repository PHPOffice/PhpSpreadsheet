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

    public function providerCOMPLEX()
    {
        return require 'data/Calculation/Engineering/COMPLEX.php';
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
