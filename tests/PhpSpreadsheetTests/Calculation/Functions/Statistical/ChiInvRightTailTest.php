<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ChiInvRightTailTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCHIINV
     *
     * @param mixed $expectedResult
     */
    public function testCHIINV($expectedResult, ...$args): void
    {
        $this->runTestCases('CHISQ.INV.RT', $expectedResult, ...$args);
    }

    public static function providerCHIINV(): array
    {
        return require 'tests/data/Calculation/Statistical/CHIINVRightTail.php';
    }

    public function invVersusDistTest(): void
    {
        $expectedResult = 8.383430828608;
        $probability = 0.3;
        $degrees = 7;
        $calculation = Calculation::getInstance();
        $formula = "=CHISQ.INV.RT($probability, $degrees)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-8);
        $formula = "=CHISQ.DIST.RT($result, $degrees)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($probability, $result, 1.0e-8);
    }

    /**
     * @dataProvider providerChiInvRightTailArray
     */
    public function testChiInvRightTailArray(array $expectedResult, string $probabilities, string $degrees): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=CHISQ.INV.RT({$probabilities}, {$degrees})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerChiInvRightTailArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [7.8061229155968075, 6.345811195521517, 100.0],
                    [13.266097125199911, 11.34032237742413, 24.388802783239434],
                ],
                '{0.35, 0.5, 0.018}',
                '{7; 12}',
            ],
        ];
    }
}
