<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PHPUnit\Framework\TestCase;

class ErrorTypeTest extends TestCase
{
    public function testErrorTypeNoArgument(): void
    {
        $result = ExcelError::type();
        self::assertSame(ExcelError::NA(), $result);
    }

    /**
     * @dataProvider providerErrorType
     *
     * @param int|string $expectedResult
     * @param mixed $value
     */
    public function testErrorType($expectedResult, $value): void
    {
        $result = ExcelError::type($value);
        self::assertSame($expectedResult, $result);
    }

    public static function providerErrorType(): array
    {
        return require 'tests/data/Calculation/Information/ERROR_TYPE.php';
    }

    /**
     * @dataProvider providerErrorTypeArray
     */
    public function testErrorTypeArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ERROR.TYPE({$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerErrorTypeArray(): array
    {
        return [
            'vector' => [
                [[2, 4, 7, ExcelError::NA(), ExcelError::NA(), ExcelError::NA(), 5]],
                '{5/0, "#REF!", "#N/A", 1.2, TRUE, "PHP", "#NAME?"}',
            ],
        ];
    }
}
