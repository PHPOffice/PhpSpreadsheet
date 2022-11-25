<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class GeStepTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerGESTEP
     *
     * @param mixed $expectedResult
     */
    public function testGESTEP($expectedResult, ...$args): void
    {
        $this->runTestCase('GESTEP', $expectedResult, ...$args);
    }

    public function providerGESTEP(): array
    {
        return require 'tests/data/Calculation/Engineering/GESTEP.php';
    }

    /**
     * @dataProvider providerGeStepArray
     */
    public function testGeStepArray(array $expectedResult, string $a, string $b): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=GESTEP({$a}, {$b})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerGeStepArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [1, 1, 1, 1, 1],
                    [0, 1, 1, 1, 1],
                    [0, 1, 1, 1, 0],
                    [0, 1, 0, 1, 0],
                    [0, 1, 0, 0, 0],
                ],
                '{-1.2, 2.5, 0.0, 0.25, -0.5}',
                '{-1.2; -0.5; 0.0; 0.25; 2.5}',
            ],
        ];
    }
}
