<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class RowsTest extends TestCase
{
    /**
     * @dataProvider providerROWS
     */
    public function testROWS(mixed $expectedResult, null|array|string $arg): void
    {
        $result = LookupRef\RowColumnInformation::ROWS($arg);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerROWS(): array
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

    public static function providerRowsArray(): array
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
