<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class IfNaTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIFNA
     *
     * @param mixed $expectedResult
     * @param mixed $value
     * @param mixed $return
     */
    public function testIFNA($expectedResult, $value, $return): void
    {
        $result = Logical::IFNA($value, $return);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIFNA(): array
    {
        return require 'tests/data/Calculation/Logical/IFNA.php';
    }

    /**
     * @dataProvider providerIfNaArray
     */
    public function testIfNaArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IFNA({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIfNaArray(): array
    {
        return [
            'vector' => [
                [[2.5, '#DIV/0!', 6]],
                '{5/2, 5/0, "#N/A"}',
                'MAX(ABS({-2,4,-6}))',
            ],
            'return value' => [
                [[2.5, '#DIV/0!', [[2, 3, 4]]]],
                '{5/2, 5/0, "#N/A"}',
                '{2,3,4}',
            ],
        ];
    }
}
