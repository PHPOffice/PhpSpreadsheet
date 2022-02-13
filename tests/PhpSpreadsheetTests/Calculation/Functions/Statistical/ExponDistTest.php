<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class ExponDistTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerEXPONDIST
     *
     * @param mixed $expectedResult
     */
    public function testEXPONDIST($expectedResult, ...$args): void
    {
        $result = Statistical::EXPONDIST(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerEXPONDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/EXPONDIST.php';
    }

    /**
     * @dataProvider providerExponDistArray
     */
    public function testExponDistArray(array $expectedResult, string $values, string $lambdas): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=EXPONDIST({$values}, {$lambdas}, false)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerExponDistArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [1.646434908282079, 0.6693904804452895, 0.2721538598682374],
                    [1.353352832366127, 0.06737946999085467, 0.003354626279025118],
                ],
                '{0.2, 0.5, 0.8}',
                '{3; 10}',
            ],
        ];
    }
}
