<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ImAbsTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIMABS
     *
     * @param mixed $expectedResult
     */
    public function testIMABS($expectedResult, ...$args): void
    {
        $this->runTestCase('IMABS', $expectedResult, ...$args);
    }

    public function providerIMABS(): array
    {
        return require 'tests/data/Calculation/Engineering/IMABS.php';
    }

    /**
     * @dataProvider providerImAbsArray
     */
    public function testImAbsArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMABS({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImAbsArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [2.692582403567252, 2.5, 2.692582403567252],
                    [1.4142135623730951, 1.0, 1.4142135623730951],
                    [1.4142135623730951, 1.0, 1.4142135623730951],
                    [2.692582403567252, 2.5, 2.692582403567252],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
