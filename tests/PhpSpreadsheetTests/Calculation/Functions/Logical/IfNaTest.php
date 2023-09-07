<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class IfNaTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIFNA
     */
    public function testIFNA(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('IFNA', $expectedResult, ...$args);
    }

    public static function providerIFNA(): array
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

    public static function providerIfNaArray(): array
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
