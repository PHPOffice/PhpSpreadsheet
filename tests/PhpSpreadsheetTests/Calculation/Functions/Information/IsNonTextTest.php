<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Information\Value;
use PHPUnit\Framework\TestCase;

class IsNonTextTest extends TestCase
{
    public function testIsNonTextNoArgument(): void
    {
        $result = Value::isNonText();
        self::assertTrue($result);
    }

    /**
     * @dataProvider providerIsNonText
     */
    public function testIsNonText(bool $expectedResult, mixed $value): void
    {
        $result = Value::isNonText($value);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIsNonText(): array
    {
        return require 'tests/data/Calculation/Information/IS_NONTEXT.php';
    }

    /**
     * @dataProvider providerIsNonTextArray
     */
    public function testIsNonTextArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ISNONTEXT({$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIsNonTextArray(): array
    {
        return [
            'vector' => [
                [[true, false, false, true, true]],
                '{-2, "PHP", "123.456", false, 2.34}',
            ],
        ];
    }
}
