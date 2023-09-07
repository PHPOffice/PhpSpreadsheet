<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class GammaLnTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerGAMMALN
     */
    public function testGAMMALN(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('GAMMALN', $expectedResult, ...$args);
    }

    public static function providerGAMMALN(): array
    {
        return require 'tests/data/Calculation/Statistical/GAMMALN.php';
    }

    /**
     * @dataProvider providerGammaLnArray
     */
    public function testGammaLnArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=GAMMALN({$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerGammaLnArray(): array
    {
        return [
            'matrix' => [
                [['#NUM!', 1.5240638224308496], [0.20328095143131059, 2.8813232759012433]],
                '{-1.5, 0.2; 0.75, 4.8}',
            ],
        ];
    }
}
