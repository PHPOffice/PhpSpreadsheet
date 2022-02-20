<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class IsNonTextTest extends TestCase
{
    public function testIsNonTextNoArgument(): void
    {
        $result = Functions::isNonText();
        self::assertTrue($result);
    }

    /**
     * @dataProvider providerIsNonText
     *
     * @param mixed $value
     */
    public function testIsNonText(bool $expectedResult, $value): void
    {
        $result = Functions::isNonText($value);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIsNonText(): array
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

    public function providerIsNonTextArray(): array
    {
        return [
            'vector' => [
                [[true, false, false, true, true]],
                '{-2, "PHP", "123.456", false, 2.34}',
            ],
        ];
    }
}
