<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class IfErrorTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIFERROR
     *
     * @param mixed $expectedResult
     * @param mixed $value
     * @param mixed $return
     */
    public function testIFERROR($expectedResult, $value, $return): void
    {
        $result = Logical::IFERROR($value, $return);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIFERROR(): array
    {
        return require 'tests/data/Calculation/Logical/IFERROR.php';
    }

    /**
     * @dataProvider providerIfErrorArray
     */
    public function testIfErrorArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IFERROR({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIfErrorArray(): array
    {
        return [
            'vector' => [
                [[2.5, 6]],
                '{5/2, 5/0}',
                'MAX(ABS({-2,4,-6}))',
            ],
            'return value' => [
                [[2.5, [[2, 3, 4]]]],
                '{5/2, 5/0}',
                '{2,3,4}',
            ],
        ];
    }
}
