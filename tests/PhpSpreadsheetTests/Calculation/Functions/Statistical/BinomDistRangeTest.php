<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class BinomDistRangeTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerBINOMDISTRANGE
     *
     * @param mixed $expectedResult
     */
    public function testBINOMDISTRANGE($expectedResult, ...$args): void
    {
        $this->runTestCaseReference('BINOM.DIST.RANGE', $expectedResult, ...$args);
    }

    public static function providerBINOMDISTRANGE(): array
    {
        return require 'tests/data/Calculation/Statistical/BINOMDISTRANGE.php';
    }

    /**
     * @dataProvider providerBinomDistRangeArray
     */
    public function testBinomDistRangeArray(
        array $expectedResult,
        string $trials,
        string $probabilities,
        string $successes
    ): void {
        $calculation = Calculation::getInstance();

        $formula = "=BINOM.DIST.RANGE({$trials}, {$probabilities}, {$successes})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerBinomDistRangeArray(): array
    {
        return [
            'row/column vectors' => [
                [[0.17303466796875, 0.01153564453125], [0.258103609085083, 0.1032414436340332]],
                '{7; 12}',
                '0.25',
                '{3, 5}',
            ],
        ];
    }
}
