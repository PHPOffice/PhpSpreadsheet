<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class GammaDistTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerGAMMADIST
     *
     * @param mixed $expectedResult
     */
    public function testGAMMADIST($expectedResult, ...$args): void
    {
        $result = Statistical::GAMMADIST(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerGAMMADIST(): array
    {
        return require 'tests/data/Calculation/Statistical/GAMMADIST.php';
    }

    /**
     * @dataProvider providerGammaDistArray
     */
    public function testGammaDistArray(array $expectedResult, string $values, string $alpha, string $beta): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=GAMMA.DIST({$values}, {$alpha}, {$beta}, false)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerGammaDistArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [0.0012393760883331792, 0.007436256529999079, 0.0669263087699917],
                    [0.012446767091965986, 0.03734030127589798, 0.04200783893538521],
                    [0.018143590657882503, 0.043544617578918025, 0.02508169972545678],
                ],
                '12',
                '{1, 2, 5}',
                '{2; 4; 5}',
            ],
        ];
    }
}
