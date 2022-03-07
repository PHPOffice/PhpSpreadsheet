<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PHPUnit\Framework\TestCase;

class IsOddTest extends TestCase
{
    public function testIsOddNoArgument(): void
    {
        $result = Functions::isOdd();
        self::assertSame(ExcelError::NAME(), $result);
    }

    /**
     * @dataProvider providerIsOdd
     *
     * @param bool|string $expectedResult
     * @param mixed $value
     */
    public function testIsOdd($expectedResult, $value): void
    {
        $result = Functions::isOdd($value);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIsOdd(): array
    {
        return require 'tests/data/Calculation/Information/IS_ODD.php';
    }

    /**
     * @dataProvider providerIsOddArray
     */
    public function testIsOddArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ISODD({$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIsOddArray(): array
    {
        return [
            'vector' => [
                [[false, true, false, true, false]],
                '{-2, -1, 0, 1, 2}',
            ],
        ];
    }
}
