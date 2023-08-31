<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\Information\Value;
use PHPUnit\Framework\TestCase;

class IsEvenTest extends TestCase
{
    public function testIsEvenNoArgument(): void
    {
        $result = Value::isEven();
        self::assertSame(ExcelError::NAME(), $result);
    }

    /**
     * @dataProvider providerIsEven
     *
     * @param bool|string $expectedResult
     * @param mixed $value
     */
    public function testIsEven($expectedResult, $value): void
    {
        $result = Value::isEven($value);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIsEven(): array
    {
        return require 'tests/data/Calculation/Information/IS_EVEN.php';
    }

    /**
     * @dataProvider providerIsEvenArray
     */
    public function testIsEvenArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ISEVEN({$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIsEvenArray(): array
    {
        return [
            'vector' => [
                [[true, false, true, false, true]],
                '{-2, -1, 0, 1, 2}',
            ],
        ];
    }
}
