<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class ImSechTest extends TestCase
{
    const COMPLEX_PRECISION = 1E-8;

    /**
     * @var ComplexAssert
     */
    private $complexAssert;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        $this->complexAssert = new ComplexAssert();
    }

    /**
     * @dataProvider providerIMSECH
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMSECH($expectedResult, $value): void
    {
        $result = Engineering::IMSECH($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMSECH(): array
    {
        return require 'tests/data/Calculation/Engineering/IMSECH.php';
    }

    /**
     * @dataProvider providerImSecHArray
     */
    public function testImSecHArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMSECH({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImSecHArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-0.61110856415523-0.3476766607105i', '-1.2482156514688', '-0.61110856415523+0.3476766607105i'],
                    ['0.49833703055519-0.59108384172105i', '1.8508157176809', '0.49833703055519+0.59108384172105i'],
                    ['0.49833703055519+0.59108384172105i', '1.8508157176809', '0.49833703055519-0.59108384172105i'],
                    ['-0.61110856415523+0.3476766607105i', '-1.2482156514688', '-0.61110856415523-0.3476766607105i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
