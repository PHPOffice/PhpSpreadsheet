<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class IsLogicalTest extends TestCase
{
    public function testIsLogicalNoArgument(): void
    {
        $result = Functions::isLogical();
        self::assertFalse($result);
    }

    /**
     * @dataProvider providerIsLogical
     *
     * @param mixed $value
     */
    public function testIsLogical(bool $expectedResult, $value): void
    {
        $result = Functions::isLogical($value);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIsLogical(): array
    {
        return require 'tests/data/Calculation/Information/IS_LOGICAL.php';
    }

    /**
     * @dataProvider providerIsLogicalArray
     */
    public function testIsLogicalArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ISLOGICAL({$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIsLogicalArray(): array
    {
        return [
            'vector' => [
                [[true, false, false, false, true, false]],
                '{true, -1, null, 1, false, "FALSE"}',
            ],
        ];
    }
}
