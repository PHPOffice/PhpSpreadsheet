<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ErrorValue;
use PHPUnit\Framework\TestCase;

class IsNaTest extends TestCase
{
    public function testIsNaNoArgument(): void
    {
        $result = ErrorValue::isNa();
        self::assertFalse($result);
    }

    /**
     * @dataProvider providerIsNa
     */
    public function testIsNa(bool $expectedResult, mixed $value): void
    {
        $result = ErrorValue::isNa($value);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIsNa(): array
    {
        return require 'tests/data/Calculation/Information/IS_NA.php';
    }

    /**
     * @dataProvider providerIsNaArray
     */
    public function testIsNaArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ISNA({$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIsNaArray(): array
    {
        return [
            'vector' => [
                [[false, false, true, false, false, false]],
                '{5/0, "#REF!", "#N/A", 1.2, TRUE, "PHP"}',
            ],
        ];
    }
}
