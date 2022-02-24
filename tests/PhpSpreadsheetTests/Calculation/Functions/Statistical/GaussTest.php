<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class GaussTest extends TestCase
{
    /**
     * @dataProvider providerGAUSS
     *
     * @param mixed $expectedResult
     * @param mixed $testValue
     */
    public function testGAUSS($expectedResult, $testValue): void
    {
        $result = Statistical::GAUSS($testValue);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerGAUSS(): array
    {
        return require 'tests/data/Calculation/Statistical/GAUSS.php';
    }

    /**
     * @dataProvider providerGaussArray
     */
    public function testGaussArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=GAUSS({$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerGaussArray(): array
    {
        return [
            'matrix' => [
                [
                    [-0.4331927987311418, -0.28814460141660325, 0.07925970943910299],
                    [0.27337264762313174, 0.39435022633314465, 0.5],
                ],
                '{-1.5, -0.8, 0.2; 0.75, 1.25, 12.5}',
            ],
        ];
    }
}
