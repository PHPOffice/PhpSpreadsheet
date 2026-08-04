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
        ];
    }

    public function testGammaInvUnreachableTailStaysBounded(): void
    {
        // A probability the forward series never reaches cannot bracket a root,
        // so expansion stops at the original alpha*beta*5 ceiling instead of
        // running away (see Gamma::inverse doc-block).
        $result = $this->gammaInvFormulaResult(0.9999999, 1.0, 1.0);
        self::assertLessThanOrEqual(1.0 * 1.0 * 5.0, $result);
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
