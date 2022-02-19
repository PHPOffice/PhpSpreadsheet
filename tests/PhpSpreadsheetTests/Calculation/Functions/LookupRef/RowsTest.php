<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class RowsTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerROWS
     *
     * @param mixed $expectedResult
     */
    public function testROWS($expectedResult, ...$args): void
    {
        $result = LookupRef::ROWS(...$args);
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
        $result = $calculation->_calculateFormulaValue($formula);
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
