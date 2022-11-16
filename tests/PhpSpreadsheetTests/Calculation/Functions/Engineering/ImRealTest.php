<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PHPUnit\Framework\TestCase;

class ImRealTest extends TestCase
{
    const COMPLEX_PRECISION = 1E-8;

    /**
     * @dataProvider providerIMREAL
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMREAL($expectedResult, $value): void
    {
        $result = Engineering\Complex::IMREAL($value);
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    public function providerIMREAL(): array
    {
        return require 'tests/data/Calculation/Engineering/IMREAL.php';
    }

    /**
     * @dataProvider providerImRealArray
     */
    public function testImRealArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMREAL({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImRealArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [-1.0, 0.0, 1.0],
                    [-1.0, 0.0, 1.0],
                    [-1.0, 0.0, 1.0],
                    [-1.0, 0.0, 1.0],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
