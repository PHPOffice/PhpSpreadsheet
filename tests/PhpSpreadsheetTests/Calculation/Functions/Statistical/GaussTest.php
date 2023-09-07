<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class GaussTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerGAUSS
     */
    public function testGAUSS(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('GAUSS', $expectedResult, ...$args);
    }

    public static function providerGAUSS(): array
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

    public static function providerGaussArray(): array
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
