<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class BinomInvTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBINOMINV
     *
     * @param mixed $expectedResult
     */
    public function testBINOMINV($expectedResult, ...$args): void
    {
        $result = Statistical::CRITBINOM(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerBINOMINV(): array
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

    public function providerBinomInvArray(): array
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
