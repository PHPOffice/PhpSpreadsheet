<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ImaginaryTest extends TestCase
{
    const COMPLEX_PRECISION = 1E-8;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIMAGINARY
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMAGINARY($expectedResult, $value): void
    {
        $result = Engineering::IMAGINARY($value);
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    public function providerIMAGINARY(): array
    {
        return require 'tests/data/Calculation/Engineering/IMAGINARY.php';
    }

    /**
     * @dataProvider providerImaginaryArray
     */
    public function testImaginaryArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMAGINARY({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImaginaryArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [-2.5, -2.5, -2.5],
                    [-1.0, -1.0, -1.0],
                    [1.0, 1.0, 1.0],
                    [2.5, 2.5, 2.5],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
