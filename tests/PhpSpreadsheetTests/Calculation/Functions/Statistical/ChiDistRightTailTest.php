<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class ChiDistRightTailTest extends TestCase
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
        $result = Statistical::CHIDIST(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCHIDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/CHIDISTRightTail.php';
    }

    /**
     * @dataProvider providerChiDistRightTailArray
     */
    public function testChiDistRightTailArray(array $expectedResult, string $values, string $degrees): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=CHISQ.DIST.RT({$values}, {$degrees})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerChiDistRightTailArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [0.8850022316431506, 0.6599632296942824, 0.33259390259930777],
                    [0.9955440192247521, 0.9579789618046938, 0.7851303870304048],
                ],
                '{3, 5, 8}',
                '{7; 12}',
            ],
        ];
    }
}
