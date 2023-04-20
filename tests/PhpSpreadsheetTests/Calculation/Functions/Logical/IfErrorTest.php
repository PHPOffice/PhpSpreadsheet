<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class IfErrorTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIFERROR
     *
     * @param mixed $expectedResult
     */
    public function testIFERROR($expectedResult, ...$args): void
    {
        $this->runTestCase('IFERROR', $expectedResult, ...$args);
    }

    public static function providerIFERROR(): array
    {
        return require 'tests/data/Calculation/Logical/IFERROR.php';
    }

    /**
     * @dataProvider providerIfErrorArray
     */
    public function testIfErrorArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IFERROR({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIfErrorArray(): array
    {
        return [
            'vector' => [
                [[2.5, 6]],
                '{5/2, 5/0}',
                'MAX(ABS({-2,4,-6}))',
            ],
            'return value' => [
                [[2.5, [[2, 3, 4]]]],
                '{5/2, 5/0}',
                '{2,3,4}',
            ],
        ];
    }
}
