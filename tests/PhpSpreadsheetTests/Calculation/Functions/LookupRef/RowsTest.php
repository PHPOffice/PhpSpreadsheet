<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\RowColumnInformation;
use PHPUnit\Framework\TestCase;

class RowsTest extends TestCase
{
    /**
     * @dataProvider providerROWS
     *
     * @param mixed $expectedResult
     */
    public function testROWS($expectedResult, ...$args): void
    {
        $result = RowColumnInformation::rows(/** @scrutinizer ignore-type */ ...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerROWS(): array
    {
        return require 'tests/data/Calculation/LookupRef/ROWS.php';
    }

    /**
     * @dataProvider providerRowsArray
     */
    public function testRowsArray(int $expectedResult, string $argument): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ROWS({$argument})";
        $result = $calculation->calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerRowsArray(): array
    {
        return [
            [
                2,
                '{1,2,3;4,5,6}',
            ],
            [
                1,
                '{1,2,3,4,5}',
            ],
            [
                5,
                '{1;2;3;4;5}',
            ],
        ];
    }
}
