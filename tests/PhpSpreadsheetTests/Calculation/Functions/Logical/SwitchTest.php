<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class SwitchTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSwitch
     *
     * @param mixed $expectedResult
     */
    public function testSWITCH($expectedResult, ...$args): void
    {
        $this->runTestCase('SWITCH', $expectedResult, ...$args);
    }

    public static function providerSwitch(): array
    {
        return require 'tests/data/Calculation/Logical/SWITCH.php';
    }

    /**
     * @dataProvider providerSwitchArray
     *
     * @param mixed $expression
     * @param mixed $value1
     * @param mixed $value2
     */
    public function testIfsArray(array $expectedResult, $expression, $value1, string $result1, $value2, string $result2, string $default): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=SWITCH($expression, $value1, {" . "$result1}, $value2, {" . "$result2}, {" . "$default})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerSwitchArray(): array
    {
        return [
            'Array return' => [
                [[4, 5, 6]],
                2,
                1,
                '1, 2, 3',
                2,
                '4, 5, 6',
                '7, 8, 9',
            ],
            'Array return default' => [
                [[7, 8, 9]],
                3,
                1,
                '1, 2, 3',
                2,
                '4, 5, 6',
                '7, 8, 9',
            ],
        ];
    }
}
