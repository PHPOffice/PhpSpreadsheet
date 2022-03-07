<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class LookupTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerLOOKUP
     *
     * @param mixed $expectedResult
     */
    public function testLOOKUP($expectedResult, ...$args): void
    {
        $result = LookupRef::LOOKUP(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerLOOKUP(): array
    {
        return require 'tests/data/Calculation/LookupRef/LOOKUP.php';
    }

    /**
     * @dataProvider providerLookupArray
     */
    public function testLookupArray(array $expectedResult, string $values, string $lookup, string $return): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=LOOKUP({$values}, {$lookup}, {$return})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerLookupArray(): array
    {
        return [
            'row vector' => [
                [['Orange', 'Green', 'Red']],
                '{4.19, 5.77, 4.14}',
                '{4.14; 4.19; 5.17; 5.77; 6.39}',
                '{"Red"; "Orange"; "Yellow"; "Green"; "Blue"}',
            ],
        ];
    }
}
