<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class PermutationATest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPERMUT
     *
     * @param mixed $expectedResult
     */
    public function testPERMUT($expectedResult, ...$args): void
    {
        $this->runTestCases('PERMUTATIONA', $expectedResult, ...$args);
    }

    public static function providerPERMUT(): array
    {
        return require 'tests/data/Calculation/Statistical/PERMUTATIONA.php';
    }

    /**
     * @dataProvider providerPermutationAArray
     */
    public function testPermutationAArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=PERMUTATIONA({$argument1},{$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerPermutationAArray(): array
    {
        return [
            'first argument row vector' => [
                [[512, 125]],
                '{8, 5}',
                '3',
            ],
            'first argument column vector' => [
                [[512], [125]],
                '{8; 5}',
                '3',
            ],
            'first argument matrix' => [
                [[512, 125], [27, 343]],
                '{8, 5; 3, 7}',
                '3',
            ],
            'second argument row vector' => [
                [[2197, 4826809]],
                '13',
                '{3, 6}',
            ],
            'second argument column vector' => [
                [[2197], [4826809]],
                '13',
                '{3; 6}',
            ],
            'second argument matrix' => [
                [[2197, 4826809], [28561, 815730721]],
                '13',
                '{3, 6; 4, 8}',
            ],
            'A row and a column vector' => [
                [
                    [248832, 20736, 1728, 144],
                    [100000, 10000, 1000, 100],
                    [32768, 4096, 512, 64],
                    [7776, 1296, 216, 36],
                ],
                '{12; 10; 8; 6}',
                '{5, 4, 3, 2}',
            ],
            'Two row vectors' => [
                [[248832, 10000, 512, 36]],
                '{12, 10, 8, 6}',
                '{5, 4, 3, 2}',
            ],
            'Two column vectors' => [
                [[248832], [10000], [512], [36]],
                '{12; 10; 8; 6}',
                '{5; 4; 3; 2}',
            ],
        ];
    }
}
