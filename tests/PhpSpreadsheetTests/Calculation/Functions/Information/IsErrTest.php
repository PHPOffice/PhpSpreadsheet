<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ErrorValue;
use PHPUnit\Framework\TestCase;

class IsErrTest extends TestCase
{
    public function testIsErrNoArgument(): void
    {
        $result = ErrorValue::isErr();
        self::assertFalse($result);
    }

    /**
     * @dataProvider providerIsErr
     */
    public function testIsErr(bool $expectedResult, mixed $value): void
    {
        $result = ErrorValue::isErr($value);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIsErr(): array
    {
        return require 'tests/data/Calculation/Information/IS_ERR.php';
    }

    /**
     * @dataProvider providerIsErrArray
     */
    public function testIsErrArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ISERR({$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIsErrArray(): array
    {
        return [
            'vector' => [
                [[true, true, false, false, false, false]],
                '{5/0, "#REF!", "#N/A", 1.2, TRUE, "PHP"}',
            ],
        ];
    }
}
