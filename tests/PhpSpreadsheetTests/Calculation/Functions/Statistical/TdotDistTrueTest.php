<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PHPUnit\Framework\Attributes\DataProvider;

class TdotDistTrueTest extends AllSetupTeardown
{
    #[DataProvider('providerTdotDistTrue')]
    public function testTdotDistTrue(mixed $expectedResult, mixed $value, mixed $degrees): void
    {
        $this->runTestCaseReference('T.DIST', $expectedResult, $value, $degrees, true);
    }

    public static function providerTdotDistTrue(): array
    {
        return require 'tests/data/Calculation/Statistical/tDotDistTrue.php';
    }

    #[DataProvider('providerTdotDistArray')]
    public function testTdotDistArray(array $expectedResult, string $values, string $degrees): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=T.DIST({$values}, {$degrees}, true)";
        $result = $calculation->calculateFormula($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-6);
    }

    public static function providerTdotDistArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [0.852416, 0.930337, 0.959742],
                ],
                '2',
                '{1.5, 3.5, 8}',
            ],
        ];
    }
}
