<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class ChiDistLeftTailTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCHIDIST
     *
     * @param mixed $expectedResult
     */
    public function testCHIDIST($expectedResult, ...$args): void
    {
        $result = Statistical\Distributions\ChiSquared::distributionLeftTail(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCHIDIST(): array
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

    public function providerChiDistLeftTailArray(): array
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
