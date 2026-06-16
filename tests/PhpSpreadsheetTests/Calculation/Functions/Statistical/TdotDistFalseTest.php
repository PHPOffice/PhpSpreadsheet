<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PHPUnit\Framework\Attributes\DataProvider;

class TdotDistFalseTest extends AllSetupTeardown
{
    #[DataProvider('providerTdotDistFalse')]
    public function testTdotDistFalse(mixed $expectedResult, mixed $value, mixed $degrees): void
    {
        $this->runTestCaseReference('T.DIST', $expectedResult, $value, $degrees, false);
    }

    public static function providerTdotDistFalse(): array
    {
        return require 'tests/data/Calculation/Statistical/tDotDistFalse.php';
    }

    #[DataProvider('providerTdotDistArray')]
    public function testTdotDistArray(array $expectedResult, string $values, string $degrees): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=T.DIST({$values}, {$degrees}, false)";
        $result = $calculation->calculateFormula($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-6);
    }

    public static function providerTdotDistArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [0.063662, 0.0675096606638932, 0.06236808463468194],
                ],
                '2',
                '{1.5, 3.5, 8}',
            ],
        ];
    }
}
