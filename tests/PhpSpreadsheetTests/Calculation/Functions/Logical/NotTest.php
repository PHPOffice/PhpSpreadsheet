<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Operations;
use PHPUnit\Framework\TestCase;

class NotTest extends TestCase
{
    /**
     * @dataProvider providerNOT
     *
     * @param mixed $expectedResult
     */
    public function testNOT($expectedResult, ...$args): void
    {
        if (count($args) === 0) {
            $result = Operations::not();
        } else {
            $result = Operations::not($args[0]);
        }
        self::assertEquals($expectedResult, $result);
    }

    public function providerNOT(): array
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
        $result = $calculation->calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerNotArray(): array
    {
        return [
            'vector' => [
                [[false, true, true, false]],
                '{TRUE, FALSE, FALSE, TRUE}',
            ],
        ];
    }
}
