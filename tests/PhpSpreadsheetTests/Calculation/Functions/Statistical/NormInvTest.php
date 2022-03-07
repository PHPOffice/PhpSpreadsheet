<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class NormInvTest extends TestCase
{
    /**
     * @dataProvider providerNORMINV
     *
     * @param mixed $expectedResult
     */
    public function testNORMINV($expectedResult, ...$args): void
    {
        $result = Statistical::NORMINV(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerNORMINV(): array
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

    public function providerNormInvArray(): array
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
