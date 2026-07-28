<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Gamma;

class GammaInvTest extends AllSetupTeardown
{
    /**
     * Extreme upper-tail quantiles whose true root exceeds the old fixed
     * alpha*beta*5 bracket ceiling, which used to clamp the result to it.
     * Expected values from mpmath (findroot on the regularized gammainc).
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerGammaInvExtremeTail')]
    public function testGammaInvExtremeTail(float $expected, float $probability, float $alpha, float $beta): void
    {
        $x = Gamma::inverse($probability, $alpha, $beta);
        self::assertIsFloat($x);
        // Bracket ceiling was exceeded, so the fix must have expanded past it.
        self::assertGreaterThan($alpha * $beta * 5.0, $x);
        // Round-trip invariant: the quantile maps back to the input probability.
        $roundTrip = Gamma::distribution($x, $alpha, $beta, true);
        self::assertEqualsWithDelta($probability, $roundTrip, 1.0e-8);
        // And it matches the reference quantile.
        self::assertEqualsWithDelta($expected, $x, 1.0e-3);
    }

    public static function providerGammaInvExtremeTail(): array
    {
        return [
            'p=0.9999 alpha=1 beta=1' => [9.210340371976, 0.9999, 1.0, 1.0],
            'p=0.99995 alpha=1 beta=1' => [9.903487552536, 0.99995, 1.0, 1.0],
            'p=0.9999 alpha=0.5 beta=2' => [15.136705226623, 0.9999, 0.5, 2.0],
            'p=0.9999 alpha=1 beta=2' => [18.420680743952, 0.9999, 1.0, 2.0],
        ];
    }

    public function testGammaInvUnreachableTailStaysBounded(): void
    {
        // A probability the forward series cannot reach must not send the
        // bracket expansion running away to a huge nonsensical quantile.
        $x = Gamma::inverse(0.9999999, 1.0, 1.0);
        self::assertIsFloat($x);
        self::assertLessThan(1000.0, $x);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerGAMMAINV')]
    public function testGAMMAINV(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('GAMMA.INV', $expectedResult, ...$args);
    }

    public static function providerGAMMAINV(): array
    {
        return require 'tests/data/Calculation/Statistical/GAMMAINV.php';
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerGammaInvArray')]
    public function testGammaInvArray(array $expectedResult, string $values, string $alpha, string $beta): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=GAMMA.INV({$values}, {$alpha}, {$beta})";
        $result = $calculation->calculateFormula($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerGammaInvArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [2.772588722239782, 5.38526905777939, 12.548861396889375],
                    [5.545177444479563, 10.77053811555878, 25.09772279377875],
                    [6.931471805599453, 13.463172644448473, 31.372153492223436],
                ],
                '0.75',
                '{1, 2, 5}',
                '{2; 4; 5}',
            ],
        ];
    }
}
