<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class DollarDeTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDOLLARDE
     */
    public function testDOLLARDE(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('DOLLARDE', $expectedResult, $args);
    }

    public static function providerDOLLARDE(): array
    {
        return require 'tests/data/Calculation/Financial/DOLLARDE.php';
    }

    /**
     * @dataProvider providerDollarDeArray
     */
    public function testDollarDeArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=DollarDe({$argument1},{$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    public static function providerDollarDeArray(): array
    {
        return [
            'first argument row vector' => [
                [[1.125, 2.0625, -12.625, 3.5]],
                '{1.02, 2.01, -12.1, 1.4}',
                '16',
            ],
            'first argument column vector' => [
                [[1.0625], [2.03125], [-12.3125], [2.25]],
                '{1.02; 2.01; -12.1; 1.4}',
                '32',
            ],
            'first argument matrix' => [
                [[1.05, 2.25], [-12.5, 2.0]],
                '{1.02, 2.1; -12.2, 1.4}',
                '4',
            ],
            'second argument row vector' => [
                [[4.25, 3.625, 6.125, 4.5625]],
                '3.5',
                '{4, 8, 16, 32}',
            ],
            'second argument column vector' => [
                [[5.5], [4.25], [3.625], [6.125]],
                '3.5',
                '{2; 4; 8; 16}',
            ],
            'second argument matrix' => [
                [[-4.875, -3.9375], [-9.25, -7.6875]],
                '-3.75',
                '{4, 8; 12, 16}',
            ],
        ];
    }
}
