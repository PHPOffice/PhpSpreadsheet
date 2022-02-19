<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class ChiInvRightTailTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCHIINV
     *
     * @param mixed $expectedResult
     * @param mixed $probability
     * @param mixed $degrees
     */
    public function testCHIINV($expectedResult, $probability, $degrees): void
    {
        $result = Statistical::CHIINV($probability, $degrees);
        if (!is_string($expectedResult)) {
            $reverse = Statistical\Distributions\ChiSquared::distributionRightTail($result, $degrees);
            self::assertEqualsWithDelta($probability, $reverse, 1E-12);
        }
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCHIINV(): array
    {
        return require 'tests/data/Calculation/Statistical/CHIINVRightTail.php';
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

    public function providerChiInvRightTailArray(): array
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
