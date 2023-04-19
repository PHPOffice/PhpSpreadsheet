<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class BetaDistTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerBETADIST
     *
     * @param mixed $expectedResult
     */
    public function testBETADIST($expectedResult, ...$args): void
    {
        $this->runTestCaseReference('BETADIST', $expectedResult, ...$args);
    }

    public static function providerBETADIST(): array
    {
        return require 'tests/data/Calculation/Statistical/BETADIST.php';
    }

    /**
     * @dataProvider providerBetaDistArray
     */
    public function testBetaDistArray(array $expectedResult, string $argument1, string $argument2, string $argument3): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BETADIST({$argument1}, {$argument2}, {$argument3})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerBetaDistArray(): array
    {
        return [
            'row/column vectors' => [
                [[0.25846539810299873, 0.05696312425682317], [0.3698138247709718, 0.10449584381010533]],
                '0.25',
                '{5, 7.5}',
                '{10; 12}',
            ],
        ];
    }
}
