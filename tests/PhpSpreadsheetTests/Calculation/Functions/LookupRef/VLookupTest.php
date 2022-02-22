<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class VLookupTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerVLOOKUP
     *
     * @param mixed $expectedResult
     */
    public function testVLOOKUP($expectedResult, ...$args): void
    {
        $result = LookupRef::VLOOKUP(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerVLOOKUP(): array
    {
        return require 'tests/data/Calculation/LookupRef/VLOOKUP.php';
    }

    /**
     * @dataProvider providerVLookupArray
     */
    public function testVLookupArray(array $expectedResult, string $values, string $database, string $index): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=VLOOKUP({$values}, {$database}, {$index}, false)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerVLookupArray(): array
    {
        return [
            'row vector' => [
                [[4.19, 5.77, 4.14]],
                '{"Orange", "Green", "Red"}',
                '{"Red", 4.14; "Orange", 4.19; "Yellow", 5.17; "Green", 5.77; "Blue", 6.39}',
                '2',
            ],
        ];
    }
}
