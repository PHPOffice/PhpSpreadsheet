<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class PermutTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerPERMUT
     *
     * @param mixed $expectedResult
     */
    public function testPERMUT($expectedResult, ...$args): void
    {
        $result = Statistical::PERMUT(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerPERMUT(): array
    {
        return require 'tests/data/Calculation/Statistical/PERMUT.php';
    }

    /**
     * @dataProvider providerPermutArray
     */
    public function testPermutArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=PERMUT({$argument1},{$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerPermutArray(): array
    {
        return [
            'first argument row vector' => [
                [[336, 60]],
                '{8, 5}',
                '3',
            ],
            'first argument column vector' => [
                [[336], [60]],
                '{8; 5}',
                '3',
            ],
            'first argument matrix' => [
                [[336, 60], [6, 210]],
                '{8, 5; 3, 7}',
                '3',
            ],
            'second argument row vector' => [
                [[1716, 1235520]],
                '13',
                '{3, 6}',
            ],
            'second argument column vector' => [
                [[1716], [1235520]],
                '13',
                '{3; 6}',
            ],
            'second argument matrix' => [
                [[1716, 1235520], [17160, 51891840]],
                '13',
                '{3, 6; 4, 8}',
            ],
            'A row and a column vector' => [
                [
                    [95040, 11880, 1320, 132],
                    [30240, 5040, 720, 90],
                    [6720, 1680, 336, 56],
                    [720, 360, 120, 30],
                ],
                '{12; 10; 8; 6}',
                '{5, 4, 3, 2}',
            ],
            'Two row vectors' => [
                [[95040, 5040, 336, 30]],
                '{12, 10, 8, 6}',
                '{5, 4, 3, 2}',
            ],
            'Two column vectors' => [
                [[95040], [5040], [336], [30]],
                '{12; 10; 8; 6}',
                '{5; 4; 3; 2}',
            ],
        ];
    }
}
