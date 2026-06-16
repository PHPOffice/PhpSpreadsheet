<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class TDistTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerTDIST')]
    public function testTDIST(mixed $expectedResult, mixed $value, mixed $degrees, mixed $tails): void
    {
        $this->runTestCaseReference('TDIST', $expectedResult, $value, $degrees, $tails);
    }

    public static function providerTDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/TDIST.php';
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerTDistArray')]
    public function testTDistArray(array $expectedResult, string $values, string $degrees, string $tails): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=TDIST({$values}, {$degrees}, {$tails})";
        $result = $calculation->calculateFormula($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-6);
    }

    public static function providerTDistArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [0.147584, 0.06966298427942164, 0.040258118978631297],
                    [0.295167, 0.13932596855884327, 0.08051623795726259],
                ],
                '2',
                '{1.5, 3.5, 8}',
                '{1; 2}',
            ],
        ];
    }
}
