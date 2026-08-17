<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class GammaInvTest extends AllSetupTeardown
{
    private function gammaInvFormulaResult(float $probability, float $alpha, float $beta): mixed
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue($probability);
        $sheet->getCell('A2')->setValue($alpha);
        $sheet->getCell('A3')->setValue($beta);
        $sheet->getCell('B1')->setValue('=GAMMA.INV(A1, A2, A3)');

        return $sheet->getCell('B1')->getCalculatedValue();
    }

    /**
     * Upper-tail quantiles whose true root exceeds the old fixed alpha*beta*5
     * bracket ceiling that used to clamp the result to it. Reference values from
     * mpmath findroot on the regularized gammainc.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerGammaInvExtremeTail')]
    public function testGammaInvExtremeTail(float $expected, float $probability, float $alpha, float $beta): void
    {
        self::assertEqualsWithDelta($expected, $this->gammaInvFormulaResult($probability, $alpha, $beta), 1.0e-3);
    }

    public static function providerGammaInvExtremeTail(): array
    {
        return [
            'p=0.9999 alpha=1 beta=1' => [9.210340371976, 0.9999, 1.0, 1.0],
            'p=0.99995 alpha=1 beta=1' => [9.903487552536, 0.99995, 1.0, 1.0],
            'p=0.9999 alpha=0.5 beta=2' => [15.136705226623, 0.9999, 0.5, 2.0],
            'p=0.9999 alpha=1 beta=2' => [18.420680743952, 0.9999, 1.0, 2.0],
            // Past #4945, regularizedGammaP/Q are accurate at this depth, so the
            // bracket now reaches the true root instead of the old alpha*beta*5
            // ceiling; -ln(1e-7) is the closed-form check (alpha=1).
            'p=0.9999999 alpha=1 beta=1' => [16.118095650958, 0.9999999, 1.0, 1.0],
        ];
    }

    /**
     * Shape parameters whose pdf/cdf used to overflow or under-iterate: alpha in
     * ~[143, 171.62] silently returned the first bisection midpoint (alpha=143
     * gave 358.0), larger alpha gave #NUM!, and alpha above ~5000 drifted from
     * the true root. References from mpmath 50-digit findroot on regularized P.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerGammaInvLargeShape')]
    public function testGammaInvLargeShape(float $expected, float $probability, float $alpha, float $beta): void
    {
        self::assertEqualsWithDelta($expected, $this->gammaInvFormulaResult($probability, $alpha, $beta), 1.0e-6);
    }

    public static function providerGammaInvLargeShape(): array
    {
        return [
            'p=0.001 alpha=143' => [108.87480324556955, 0.001, 143.0, 1.0],
            'p=0.5 alpha=143' => [142.66680515301345, 0.5, 143.0, 1.0],
            'p=0.9999 alpha=143' => [191.80362061926086, 0.9999, 143.0, 1.0],
            'p=0.001 alpha=171.7' => [134.03805036756231, 0.001, 171.7, 1.0],
            'p=0.5 alpha=171.7' => [171.36678195559252, 0.5, 171.7, 1.0],
            'p=0.9999 alpha=171.7' => [224.75826597753954, 0.9999, 171.7, 1.0],
            'p=0.5 alpha=172' => [171.66678175408084, 0.5, 172.0, 1.0],
            'p=0.001 alpha=200' => [159.12980117448907, 0.001, 200.0, 1.0],
            'p=0.5 alpha=200' => [199.66676561246567, 0.5, 200.0, 1.0],
            'p=0.9999 alpha=200' => [256.91788180053889, 0.9999, 200.0, 1.0],
            'p=0.001 alpha=1000' => [905.12079093497662, 0.001, 1000.0, 1.0],
            'p=0.5 alpha=1000' => [999.66668642696518, 0.5, 1000.0, 1.0],
            'p=0.9999 alpha=1000' => [1121.9041774983375, 0.9999, 1000.0, 1.0],
            'p=0.5 alpha=200 beta=2' => [399.33353122493135, 0.5, 200.0, 2.0],
            'p=0.9999 alpha=500 beta=3' => [1762.4002448762386, 0.9999, 500.0, 3.0],
            'p=0.5 alpha=10000' => [9999.666668642047, 0.5, 10000.0, 1.0],
            'p=0.001 alpha=100000' => [99025.63189050092, 0.001, 100000.0, 1.0],
            'p=0.5 alpha=100000' => [99999.6666668642, 0.5, 100000.0, 1.0],
            'p=0.9999 alpha=100000' => [101180.33552637429, 0.9999, 100000.0, 1.0],
        ];
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
                    [5.545177444479561, 10.770538115558788, 25.097722793778765],
                    [6.931471805599456, 13.463172644448484, 31.372153492223447],
                ],
                '0.75',
                '{1, 2, 5}',
                '{2; 4; 5}',
            ],
        ];
    }
}
