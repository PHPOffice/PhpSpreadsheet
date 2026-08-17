<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ChiDistRightTailTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerCHIDIST')]
    public function testCHIDIST(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseReference('CHISQ.DIST.RT', $expectedResult, ...$args);
    }

    public static function providerCHIDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/CHIDISTRightTail.php';
    }

    // Deep right tail computed directly via Q(a,x); "1 - P" would collapse these to 0.
    // Expected values from mpmath (gammainc regularized, dps 30 == 35).
    #[\PHPUnit\Framework\Attributes\DataProvider('providerChiDistRightTailDeepTail')]
    public function testChiDistRightTailDeepTail(float $expectedResult, float $value, int $degrees): void
    {
        $calculation = Calculation::getInstance();
        $formula = "=CHISQ.DIST.RT($value, $degrees)";
        /** @var float $result */
        $result = $calculation->calculateFormula($formula);
        self::assertEqualsWithDelta($expectedResult, $result, abs($expectedResult) * 1.0e-9, $formula);
    }

    public static function providerChiDistRightTailDeepTail(): array
    {
        return [
            [1.7418252446695515e-16, 80.0, 4],
            [5.0600460658425739e-21, 120.0, 10],
            [1.1253473960842734e-31, 200.0, 20],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerChiDistRightTailArray')]
    public function testChiDistRightTailArray(array $expectedResult, string $values, string $degrees): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=CHISQ.DIST.RT({$values}, {$degrees})";
        $result = $calculation->calculateFormula($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerChiDistRightTailArray(): array
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
