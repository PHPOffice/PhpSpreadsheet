<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class ImCschTest extends TestCase
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
     * @dataProvider providerIMCSCH
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMCSCH($expectedResult, $value): void
    {
        $result = Engineering::IMCSCH($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMCSCH(): array
    {
        return require 'tests/data/Calculation/Engineering/IMCSCH.php';
    }

    /**
     * @dataProvider providerImCschArray
     */
    public function testImCschArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMCSCH({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImCschArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['0.54132290619+0.53096557762117i', '1.6709215455587i', '-0.54132290619+0.53096557762117i'],
                    ['-0.30393100162843+0.62151801717043i', '1.1883951057781i', '0.30393100162843+0.62151801717043i'],
                    ['-0.30393100162843-0.62151801717043i', '-1.1883951057781i', '0.30393100162843-0.62151801717043i'],
                    ['0.54132290619-0.53096557762117i', '-1.6709215455587i', '-0.54132290619-0.53096557762117i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
