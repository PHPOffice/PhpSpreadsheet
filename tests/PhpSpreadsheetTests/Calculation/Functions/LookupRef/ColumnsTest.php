<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class ColumnsTest extends TestCase
{
    /**
     * @dataProvider providerCOLUMNS
     */
    public function testCOLUMNS(mixed $expectedResult, null|array|string $arg): void
    {
        $result = LookupRef\RowColumnInformation::COLUMNS($arg);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerCOLUMNS(): array
    {
        return require 'tests/data/Calculation/LookupRef/COLUMNS.php';
    }

    /**
     * @dataProvider providerColumnsArray
     */
    public function testColumnsArray(int $expectedResult, string $argument): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=COLUMNS({$argument})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerColumnsArray(): array
    {
        return [
            [
                3,
                '{1,2,3;4,5,6}',
            ],
            [
                5,
                '{1,2,3,4,5}',
            ],
            [
                1,
                '{1;2;3;4;5}',
            ],
        ];
    }
}
