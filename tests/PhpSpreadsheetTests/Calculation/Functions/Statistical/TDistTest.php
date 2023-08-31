<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class TDistTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTDIST
     *
     * @param mixed $expectedResult
     * @param mixed $degrees
     * @param mixed $value
     * @param mixed $tails
     */
    public function testTDIST($expectedResult, $value, $degrees, $tails): void
    {
        $this->runTestCaseReference('TDIST', $expectedResult, $value, $degrees, $tails);
    }

    public static function providerTDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/TDIST.php';
    }

    /**
     * @dataProvider providerTDistArray
     */
    public function testTDistArray(array $expectedResult, string $values, string $degrees, string $tails): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=TDIST({$values}, {$degrees}, {$tails})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerTDistArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [0.020259663176916964, 0.06966298427942164, 0.040258118978631297],
                    [0.04051932635383393, 0.13932596855884327, 0.08051623795726259],
                ],
                '2',
                '{1.5, 3.5, 8}',
                '{1; 2}',
            ],
        ];
    }
}
