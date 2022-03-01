<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class NotTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerNOT
     *
     * @param mixed $expectedResult
     */
    public function testNOT($expectedResult, ...$args): void
    {
        $result = Logical::NOT(...$args);
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
        $result = $calculation->_calculateFormulaValue($formula);
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
