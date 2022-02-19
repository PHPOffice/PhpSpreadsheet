<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class ChooseTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCHOOSE
     *
     * @param mixed $expectedResult
     */
    public function testCHOOSE($expectedResult, ...$args): void
    {
        $result = LookupRef::CHOOSE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCHOOSE(): array
    {
        return require 'tests/data/Calculation/LookupRef/CHOOSE.php';
    }

    /**
     * @dataProvider providerChooseArray
     */
    public function testChooseArray(array $expectedResult, string $values, array $selections): void
    {
        $calculation = Calculation::getInstance();

        $selections = implode(',', $selections);
        $formula = "=CHOOSE({$values}, {$selections})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerChooseArray(): array
    {
        return [
            'row vector' => [
                [['Orange', 'Blue', 'Yellow']],
                '{2, 5, 3}',
                ['"Red"', '"Orange"', '"Yellow"', '"Green"', '"Blue"'],
            ],
        ];
    }
}
