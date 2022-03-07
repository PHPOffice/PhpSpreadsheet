<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class IsNumberTest extends TestCase
{
    public function testIsNumberNoArgument(): void
    {
        $result = Functions::isNumber();
        self::assertFalse($result);
    }

    /**
     * @dataProvider providerIsNumber
     *
     * @param mixed $value
     */
    public function testIsNumber(bool $expectedResult, $value): void
    {
        $result = Functions::isNumber($value);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIsNumber(): array
    {
        return require 'tests/data/Calculation/Information/IS_NUMBER.php';
    }

    /**
     * @dataProvider providerIsNumberArray
     */
    public function testIsNumberArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ISNUMBER({$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIsNumberArray(): array
    {
        return [
            'vector' => [
                [[true, false, false, false, true]],
                '{-2, "PHP", "123.456", false, 2.34}',
            ],
        ];
    }
}
