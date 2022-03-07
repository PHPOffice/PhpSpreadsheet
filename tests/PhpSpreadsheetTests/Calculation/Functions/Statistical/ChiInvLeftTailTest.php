<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class ChiInvLeftTailTest extends TestCase
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
        $result = Statistical\Distributions\ChiSquared::inverseLeftTail($probability, $degrees);
        if (!is_string($expectedResult)) {
            $reverse = Statistical\Distributions\ChiSquared::distributionLeftTail($result, $degrees, true);
            self::assertEqualsWithDelta($probability, $reverse, 1E-12);
        }
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCHIINV(): array
    {
        return require 'tests/data/Calculation/Statistical/CHIINVLeftTail.php';
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

    public function providerChiInvLeftTailArray(): array
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
