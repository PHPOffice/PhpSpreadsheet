<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Information\Value;
use PHPUnit\Framework\TestCase;

class IsTextTest extends TestCase
{
    public function testIsTextNoArgument(): void
    {
        $result = Value::isText();
        self::assertFalse($result);
    }

    /**
     * @dataProvider providerIsText
     */
    public function testIsText(bool $expectedResult, mixed $value): void
    {
        $result = Value::isText($value);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIsText(): array
    {
        return require 'tests/data/Calculation/Information/IS_TEXT.php';
    }

    /**
     * @dataProvider providerIsTextArray
     */
    public function testIsTextArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ISTEXT({$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIsTextArray(): array
    {
        return [
            'vector' => [
                [[false, true, true, false, false]],
                '{-2, "PHP", "123.456", false, 2.34}',
            ],
        ];
    }
}
