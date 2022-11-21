<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class DeltaTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDELTA
     *
     * @param mixed $expectedResult
     */
    public function testDELTA($expectedResult, ...$args): void
    {
        $this->runTestCase('DELTA', $expectedResult, ...$args);
    }

    public function providerDELTA(): array
    {
        return require 'tests/data/Calculation/Engineering/DELTA.php';
    }

    /**
     * @dataProvider providerDeltaArray
     */
    public function testDeltaArray(array $expectedResult, string $a, string $b): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=DELTA({$a}, {$b})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerDeltaArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [1, 0, 0, 0, 0],
                    [0, 1, 0, 0, 0],
                    [0, 0, 1, 0, 0],
                    [0, 0, 0, 1, 0],
                    [0, 0, 0, 0, 1],
                ],
                '{-1.2, -0.5, 0.0, 0.25, 2.5}',
                '{-1.2; -0.5; 0.0; 0.25; 2.5}',
            ],
        ];
    }
}
