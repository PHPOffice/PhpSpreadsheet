<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class IsBlankTest extends TestCase
{
    public function testIsBlankNoArgument(): void
    {
        $result = Functions::isBlank();
        self::assertTrue($result);
    }

    /**
     * @dataProvider providerIsBlank
     *
     * @param mixed $value
     */
    public function testIsBlank(bool $expectedResult, $value): void
    {
        $result = Functions::isBlank($value);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIsBlank(): array
    {
        return require 'tests/data/Calculation/Information/IS_BLANK.php';
    }

    /**
     * @dataProvider providerIsBlankArray
     */
    public function testIsBlankArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ISBLANK({$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIsBlankArray(): array
    {
        return [
            'vector' => [
                [[false, true, false]],
                '{12, NULL, ""}',
            ],
        ];
    }
}
