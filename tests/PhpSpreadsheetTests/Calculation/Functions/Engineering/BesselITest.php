<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class BesselITest extends AllSetupTeardown
{
    /**
     * @dataProvider providerBESSELI
     *
     * @param mixed $expectedResult
     */
    public function testBESSELI($expectedResult, ...$args): void
    {
        $this->runTestCase('BESSELI', $expectedResult, ...$args);
    }

    public function providerBESSELI(): array
    {
        return require 'tests/data/Calculation/Engineering/BESSELI.php';
    }

    /**
     * @dataProvider providerBesselIArray
     */
    public function testBesselIArray(array $expectedResult, string $value, string $ord): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BESSELI({$value}, {$ord})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerBesselIArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [1.393725572000487, 1.0634833439946074, 1.0, 1.0156861326120836, 3.2898391723912908],
                    [-0.7146779363262508, -0.25789430328903556, 0.0, 0.12597910862299733, 2.516716242025361],
                    [0.20259567978255663, 0.031906148375295325, 0.0, 0.007853269593280343, 1.276466158815611],
                ],
                '{-1.2, -0.5, 0.0, 0.25, 2.5}',
                '{0; 1; 2}',
            ],
        ];
    }
}
