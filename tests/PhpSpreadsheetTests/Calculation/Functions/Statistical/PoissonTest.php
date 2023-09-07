<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class PoissonTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPOISSON
     */
    public function testPOISSON(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('POISSON', $expectedResult, ...$args);
    }

    public static function providerPOISSON(): array
    {
        return require 'tests/data/Calculation/Statistical/POISSON.php';
    }

    /**
     * @dataProvider providerPoissonArray
     */
    public function testPoissonArray(array $expectedResult, string $values, string $mean): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=POISSON({$values}, {$mean}, false)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerPoissonArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [0.4749316557209494, 0.41547307344655265, 0.028388126533408903],
                    [0.3081373033023279, 0.3299417281086086, 0.0867439330307142],
                    [0.14758417287196898, 0.19139299302082188, 0.1804470443154836],
                ],
                '{0.125, 0.5, 3}',
                '{0.7; 1.2; 2}',
            ],
        ];
    }
}
