<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class StandardizeTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSTANDARDIZE
     */
    public function testSTANDARDIZE(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('STANDARDIZE', $expectedResult, ...$args);
    }

    public static function providerSTANDARDIZE(): array
    {
        return require 'tests/data/Calculation/Statistical/STANDARDIZE.php';
    }

    /**
     * @dataProvider providerStandardizeArray
     */
    public function testStandardizeArray(array $expectedResult, string $argument1, string $argument2, string $argument3): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=STANDARDIZE({$argument1}, {$argument2}, {$argument3})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerStandardizeArray(): array
    {
        return [
            'row vector' => [[[-1.6666666666666667, -4.6666666666666667, -7.333333333333333, -10, -11.333333333333334]], '{12.5, 8, 4, 0, -2}', '15', '1.5'],
            'column vector' => [[[0.25], [0.0], [-1.0]], '{5.5; 5; 3}', '5.0', '2.0'],
            'matrix' => [[[0.25, -1.0], [-1.75, -2.75]], '{5.5, 3; 1.5, -0.5}', '5.0', '2.0'],
        ];
    }
}
