<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ChiInvLeftTailTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCHIINV
     *
     * @param mixed $expectedResult
     */
    public function testCHIINV($expectedResult, ...$args): void
    {
        $this->runTestCaseReference('CHISQ.INV', $expectedResult, ...$args);
    }

    public static function providerCHIINV(): array
    {
        return require 'tests/data/Calculation/Statistical/CHIINVLeftTail.php';
    }

    public function invVersusDistTest(): void
    {
        $expectedResult = 4.671330448981;
        $probability = 0.3;
        $degrees = 7;
        $calculation = Calculation::getInstance();
        $formula = "=CHISQ.INV($probability, $degrees)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-8);
        $formula = "=CHISQ.DIST($result, $degrees)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($probability, $result, 1.0e-8);
    }

    /**
     * @dataProvider providerChiInvLeftTailArray
     */
    public function testChiInvLeftTailArray(array $expectedResult, string $probabilities, string $degrees): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=CHISQ.INV({$probabilities}, {$degrees})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerChiInvLeftTailArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [5.0816470296362795, 6.345811195521517, 1.508898337422818],
                    [9.61151737996991, 11.34032237742413, 4.0773397083341045],
                ],
                '{0.35, 0.5, 0.018}',
                '{7; 12}',
            ],
        ];
    }
}
