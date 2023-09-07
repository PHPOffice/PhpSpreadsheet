<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class NormInvTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerNORMINV
     */
    public function testNORMINV(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('NORMINV', $expectedResult, ...$args);
    }

    public static function providerNORMINV(): array
    {
        return require 'tests/data/Calculation/Statistical/NORMINV.php';
    }

    /**
     * @dataProvider providerNormInvArray
     */
    public function testNormInvArray(array $expectedResult, string $probabilities, string $mean, string $stdDev): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=NORMINV({$probabilities}, {$mean}, {$stdDev})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerNormInvArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [2.651020499553155, 4.651020499553155],
                    [1.9765307493297324, 3.9765307493297324],
                    [-0.7214282515639576, 1.2785717484360424],
                ],
                '0.25',
                '{4, 6}',
                '{2; 3; 7}',
            ],
        ];
    }
}
