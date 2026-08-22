<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PHPUnit\Framework\Attributes\DataProvider;

class TDistRTTest extends AllSetupTeardown
{
    #[DataProvider('providerTDISTRT')]
    public function testTDISTRT(mixed $expectedResult, mixed $value, mixed $degrees): void
    {
        $this->runTestCaseReference('T.DIST.RT', $expectedResult, $value, $degrees);
    }

    public static function providerTDISTRT(): array
    {
        return require 'tests/data/Calculation/Statistical/TDISTRT.php';
    }

    #[DataProvider('providerTDistRTArray')]
    public function testTDistRTArray(array $expectedResult, string $values, string $degrees): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=T.DIST.RT({$values}, {$degrees})";
        $result = $calculation->calculateFormula($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-6);
    }

    public static function providerTDistRTArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [0.147584, 0.06966298427942164, 0.040258118978631297],
                ],
                '2',
                '{1.5, 3.5, 8}',
            ],
        ];
    }
}
