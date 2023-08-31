<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class BinomInvTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerBINOMINV
     *
     * @param mixed $expectedResult
     */
    public function testBINOMINV($expectedResult, ...$args): void
    {
        $this->runTestCaseReference('BINOM.INV', $expectedResult, ...$args);
    }

    public static function providerBINOMINV(): array
    {
        return require 'tests/data/Calculation/Statistical/BINOMINV.php';
    }

    /**
     * @dataProvider providerBinomInvArray
     */
    public function testBinomInvArray(
        array $expectedResult,
        string $trials,
        string $probabilities,
        string $alphas
    ): void {
        $calculation = Calculation::getInstance();

        $formula = "=BINOM.INV({$trials}, {$probabilities}, {$alphas})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerBinomInvArray(): array
    {
        return [
            'row/column vectors' => [
                [[32, 53], [25, 44]],
                '100',
                '{0.3, 0.5}',
                '{0.7; 0.12}',
            ],
        ];
    }
}
