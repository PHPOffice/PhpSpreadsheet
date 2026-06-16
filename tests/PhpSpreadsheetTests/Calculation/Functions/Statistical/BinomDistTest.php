<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class BinomDistTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerBINOMDIST')]
    public function testBINOMDIST(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseReference('BINOMDIST', $expectedResult, ...$args);
    }

    public static function providerBINOMDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/BINOMDIST.php';
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerBinomDistArray')]
    public function testBinomDistArray(
        array $expectedResult,
        string $values,
        string $trials,
        string $probabilities
    ): void {
        $calculation = Calculation::getInstance();

        $formula = "=BINOMDIST({$values}, {$trials}, {$probabilities}, false)";
        $result = $calculation->calculateFormula($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerBinomDistArray(): array
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
