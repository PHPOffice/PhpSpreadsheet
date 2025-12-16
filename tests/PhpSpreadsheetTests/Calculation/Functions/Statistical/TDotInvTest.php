<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PHPUnit\Framework\Attributes\DataProvider;

class TDotInvTest extends AllSetupTeardown
{
    #[DataProvider('providerTdotINV')]
    public function testTdotINV(mixed $expectedResult, mixed $probability, mixed $degrees): void
    {
        $this->runTestCaseReference('T.INV', $expectedResult, $probability, $degrees);
    }

    public static function providerTdotINV(): array
    {
        return require 'tests/data/Calculation/Statistical/TdotINV.php';
    }

    #[DataProvider('providerTInvArray')]
    public function testTInvArray(array $expectedResult, string $values, string $degrees): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=T.INV({$values}, {$degrees})";
        $result = $calculation->calculateFormula($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-6);
    }

    public static function providerTInvArray(): array
    {
        return [
            'row vector' => [
                [
                    [0.509525, 0.424202, 0.399469],
                ],
                '0.65',
                '{1.5, 3.5, 8}',
            ],
        ];
    }
}
