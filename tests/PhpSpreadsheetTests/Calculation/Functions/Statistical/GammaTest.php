<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class GammaTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerGAMMA
     */
    public function testGAMMA(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('GAMMA', $expectedResult, ...$args);
    }

    public static function providerGAMMA(): array
    {
        return require 'tests/data/Calculation/Statistical/GAMMA.php';
    }

    /**
     * @dataProvider providerGammaArray
     */
    public function testGammaArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=GAMMA({$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerGammaArray(): array
    {
        return [
            'matrix' => [
                [[2.363271800901467, 4.590843711999102], [1.2254167024651963, 17.837861981813575]],
                '{-1.5, 0.2; 0.75, 4.8}',
            ],
        ];
    }
}
