<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PHPUnit\Framework\Attributes\DataProvider;

class TDist2TTest extends AllSetupTeardown
{
    #[DataProvider('providerTDIST2T')]
    public function testTDIST2T(mixed $expectedResult, mixed $value, mixed $degrees): void
    {
        $this->runTestCaseReference('T.DIST.2T', $expectedResult, $value, $degrees);
    }

    public static function providerTDIST2T(): array
    {
        return require 'tests/data/Calculation/Statistical/TDIST2T.php';
    }

    #[DataProvider('providerTDistArray')]
    public function testTDist2TArray(array $expectedResult, string $values, string $degrees): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=T.DIST.2T({$values}, {$degrees})";
        $result = $calculation->calculateFormula($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-6);
    }

    public static function providerTDistArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [0.295167, 0.13932596855884327, 0.08051623795726259],
                ],
                '2',
                '{1.5, 3.5, 8}',
            ],
        ];
    }
}
