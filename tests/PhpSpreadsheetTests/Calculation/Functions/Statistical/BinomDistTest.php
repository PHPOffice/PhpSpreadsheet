<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class BinomDistTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBINOMDIST
     *
     * @param mixed $expectedResult
     */
    public function testBINOMDIST($expectedResult, ...$args): void
    {
        $result = Statistical::BINOMDIST(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerBINOMDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/BINOMDIST.php';
    }

    /**
     * @dataProvider providerBinomDistArray
     */
    public function testBinomDistArray(
        array $expectedResult,
        string $values,
        string $trials,
        string $probabilities
    ): void {
        $calculation = Calculation::getInstance();

        $formula = "=BINOMDIST({$values}, {$trials}, {$probabilities}, false)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerBinomDistArray(): array
    {
        return [
            'row/column vectors' => [
                [[0.17303466796875, 0.01153564453125], [0.258103609085083, 0.1032414436340332]],
                '{3, 5}',
                '{7; 12}',
                '0.25',
            ],
        ];
    }
}
