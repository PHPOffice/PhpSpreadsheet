<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class BetaInvTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerBETAINV')]
    public function testBETAINV(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseReference('BETAINV', $expectedResult, ...$args);
    }

    public static function providerBETAINV(): array
    {
        return require 'tests/data/Calculation/Statistical/BETAINV.php';
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerBetaInvArray')]
    public function testBetaInvArray(array $expectedResult, string $argument1, string $argument2, string $argument3): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BETAINV({$argument1}, {$argument2}, {$argument3})";
        $result = $calculation->calculateFormula($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerBetaInvArray(): array
    {
        return [
            'row/column vectors' => [
                [[0.24709953547, 0.346789605377], [0.215382947588, 0.307844847105]],
                '0.25',
                '{5, 7.5}',
                '{10; 12}',
            ],
        ];
    }

    /**
     * Beta(alpha, alpha) is symmetric about 0.5, so its median is exactly 0.5.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerSymmetricShape')]
    public function testBetaInvMedianOfSymmetricDistribution(float $alpha): void
    {
        $result = Calculation::getInstance()->calculateFormula("=BETAINV(0.5, $alpha, $alpha)");
        self::assertEqualsWithDelta(0.5, $result, 1.0e-12);
    }

    public static function providerSymmetricShape(): array
    {
        $cases = [];
        foreach ([0.5, 2, 20, 200, 1000, 2000, 5000, 10000, 20000, 100000] as $alpha) {
            $cases["alpha = beta = $alpha"] = [(float) $alpha];
        }

        return $cases;
    }

    /**
     * The Beta(alpha, 1) CDF is x ** alpha, so its inverse is probability ** (1 / alpha).
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerClosedForm')]
    public function testBetaInvClosedFormForBetaOne(float $probability, float $alpha): void
    {
        $result = Calculation::getInstance()->calculateFormula("=BETAINV($probability, $alpha, 1)");
        self::assertEqualsWithDelta($probability ** (1 / $alpha), $result, 1.0e-12);
    }

    public static function providerClosedForm(): array
    {
        $cases = [];
        foreach ([0.001, 0.05, 0.25, 0.5, 0.9, 0.999] as $probability) {
            foreach ([1, 10, 500, 1100, 5000, 20000, 100000] as $alpha) {
                $cases["probability $probability, alpha $alpha"] = [$probability, (float) $alpha];
            }
        }

        return $cases;
    }

    /**
     * BETADIST must map the quantile BETAINV produced back onto the probability
     * it was asked for, and BETAINV(p, a, b) must mirror 1 - BETAINV(1 - p, b, a).
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerShapeGrid')]
    public function testBetaInvAgreesWithBetaDist(float $probability, float $alpha, float $beta): void
    {
        $calculation = Calculation::getInstance();

        $roundTrip = $calculation->calculateFormula("=BETADIST(BETAINV($probability, $alpha, $beta), $alpha, $beta)");
        self::assertEqualsWithDelta($probability, $roundTrip, 1.0e-6, 'round trip through BETADIST');

        $mirror = $calculation->calculateFormula(
            "=BETAINV($probability, $alpha, $beta) + BETAINV(" . (1 - $probability) . ", $beta, $alpha)"
        );
        self::assertEqualsWithDelta(1.0, $mirror, 1.0e-12, 'mirror identity');
    }

    public static function providerShapeGrid(): array
    {
        $cases = [];
        foreach ([0.01, 0.1, 0.5, 0.9, 0.99] as $probability) {
            foreach ([[0.5, 0.5], [2, 5], [50, 50], [1000, 2], [5000, 3], [5000, 5000], [20000, 50], [200, 20000]] as [$alpha, $beta]) {
                $cases["probability $probability, alpha $alpha, beta $beta"] = [$probability, (float) $alpha, (float) $beta];
            }
        }

        return $cases;
    }
}
