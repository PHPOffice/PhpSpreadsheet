<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ChiDistLeftTailTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCHIDIST
     *
     * @param mixed $expectedResult
     */
    public function testCHIDIST($expectedResult, ...$args): void
    {
        $this->runTestCaseReference('CHISQ.DIST', $expectedResult, ...$args);
    }

    public static function providerCHIDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/CHIDISTLeftTail.php';
    }

    /**
     * @dataProvider providerChiDistLeftTailArray
     */
    public function testChiDistLeftTailArray(array $expectedResult, string $values, string $degrees): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=CHISQ.DIST({$values}, {$degrees}, false)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerChiDistLeftTailArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [0.0925081978822616, 0.12204152134938745, 0.0881791375107928],
                    [0.007059977723446427, 0.03340047144527138, 0.07814672592526599],
                ],
                '{3, 5, 8}',
                '{7; 12}',
            ],
        ];
    }
}
