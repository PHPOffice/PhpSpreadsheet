<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PHPUnit\Framework\Attributes\DataProvider;

// Note that TINV and T.INV.2T should return same results,
// so this member just borrows the TINV data.
class TDotInvDot2TTest extends AllSetupTeardown
{
    #[DataProvider('providerTINV')]
    public function testTdotINV(mixed $expectedResult, mixed $probability, mixed $degrees): void
    {
        $this->runTestCaseReference('T.INV.2T', $expectedResult, $probability, $degrees);
    }

    public static function providerTINV(): array
    {
        return require 'tests/data/Calculation/Statistical/TINV.php';
    }

    #[DataProvider('providerTInvArray')]
    public function testTInvArray(array $expectedResult, string $values, string $degrees): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=T.INV.2T({$values}, {$degrees})";
        $result = $calculation->calculateFormula($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-9);
    }

    public static function providerTInvArray(): array
    {
        return [
            'row vector' => [
                [
                    [0.612800788, 0.5023133547575189, 0.4713169827948964],
                ],
                '0.65',
                '{1.5, 3.5, 8}',
            ],
        ];
    }
}
