<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class NegBinomDistTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerNEGBINOMDIST
     *
     * @param mixed $expectedResult
     */
    public function testNEGBINOMDIST($expectedResult, ...$args): void
    {
        $result = Statistical::NEGBINOMDIST(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerNEGBINOMDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/NEGBINOMDIST.php';
    }

    /**
     * @dataProvider providerNegBinomDistArray
     */
    public function testNegBinomDistArray(
        array $expectedResult,
        string $failures,
        string $successes,
        string $probabilities
    ): void {
        $calculation = Calculation::getInstance();

        $formula = "=NEGBINOMDIST({$failures}, {$successes}, {$probabilities})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerNegBinomDistArray(): array
    {
        return [
            'row/column vectors' => [
                [[0.07508468627929688, 0.04301726818084717], [0.04503981303423643, 0.05629976629279554]],
                '{7; 12}',
                '{3, 5}',
                '0.25',
            ],
        ];
    }
}
