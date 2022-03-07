<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class GammaLnTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerGAMMALN
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testGAMMALN($expectedResult, $value): void
    {
        $result = Statistical::GAMMALN($value);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerGAMMALN(): array
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

    public function providerGammaLnArray(): array
    {
        return [
            'matrix' => [
                [['#NUM!', 1.5240638224308496], [0.20328095143131059, 2.8813232759012433]],
                '{-1.5, 0.2; 0.75, 4.8}',
            ],
        ];
    }
}
