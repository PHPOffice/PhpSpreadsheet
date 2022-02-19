<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ImArgumentTest extends TestCase
{
    const COMPLEX_PRECISION = 1E-8;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIMARGUMENT
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMARGUMENT($expectedResult, $value): void
    {
        $result = Engineering::IMARGUMENT($value);
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    public function providerIMARGUMENT(): array
    {
        return require 'tests/data/Calculation/Engineering/IMARGUMENT.php';
    }

    /**
     * @dataProvider providerImArgumentArray
     */
    public function testImArgumentArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMARGUMENT({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImArgumentArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [-1.9513027039072615, -1.5707963267948966, -1.1902899496825317],
                    [-2.356194490192345, -1.5707963267948966, -0.7853981633974483],
                    [2.356194490192345, 1.5707963267948966, 0.7853981633974483],
                    [1.9513027039072615, 1.5707963267948966, 1.1902899496825317],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
