<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class NotTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerNOT
     *
     * @param mixed $expectedResult
     */
    public function testNOT($expectedResult, ...$args): void
    {
        $this->runTestCase('NOT', $expectedResult, ...$args);
    }

    public static function providerNOT(): array
    {
        return require 'tests/data/Calculation/Logical/NOT.php';
    }

    /**
     * @dataProvider providerNotArray
     */
    public function testNotArray(array $expectedResult, string $argument1): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=NOT({$argument1})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerNotArray(): array
    {
        return [
            'vector' => [
                [[false, true, true, false]],
                '{TRUE, FALSE, FALSE, TRUE}',
            ],
        ];
    }
}
