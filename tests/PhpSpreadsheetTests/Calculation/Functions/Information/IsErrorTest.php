<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ErrorValue;
use PHPUnit\Framework\TestCase;

class IsErrorTest extends TestCase
{
    public function testIsErrorNoArgument(): void
    {
        $result = ErrorValue::isError();
        self::assertFalse($result);
    }

    /**
     * @dataProvider providerIsError
     *
     * @param mixed $value
     */
    public function testIsError(bool $expectedResult, $value): void
    {
        $result = ErrorValue::isError($value);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIsError(): array
    {
        return require 'tests/data/Calculation/Information/IS_ERROR.php';
    }

    /**
     * @dataProvider providerIsErrorArray
     */
    public function testIsErrorArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ISERROR({$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIsErrorArray(): array
    {
        return [
            'vector' => [
                [[true, true, true, false, false, false, false]],
                '{5/0, "#REF!", "#N/A", 1.2, TRUE, "PHP", null}',
            ],
        ];
    }
}
