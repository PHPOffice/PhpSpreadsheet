<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class ForecastTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerFORECAST
     *
     * @param mixed $expectedResult
     */
    public function testFORECAST($expectedResult, ...$args): void
    {
        $result = Statistical::FORECAST(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerFORECAST(): array
    {
        return require 'tests/data/Calculation/Statistical/FORECAST.php';
    }

    /**
     * @dataProvider providerForecastArray
     */
    public function testForecastArray(array $expectedResult, string $testValues, string $yValues, string $xValues): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=FORECAST({$testValues}, {$yValues}, {$xValues})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerForecastArray(): array
    {
        return [
            'row vector' => [[[-11.047619047619047, 22.95238095238095, 42.38095238095237]], '{-2, 5, 9}', '{3, 7, 15, 20, 22, 27}', '{1, 2, 3, 4, 5, 6}'],
            'column vector' => [[[-11.047619047619047], [22.95238095238095], [42.38095238095237]], '{-2; 5; 9}', '{3, 7, 15, 20, 22, 27}', '{1, 2, 3, 4, 5, 6}'],
            'matrix' => [[[-11.047619047619047, 22.95238095238095], [42.38095238095237, 15.66666666666666]], '{-2, 5; 9, 3.5}', '{3, 7, 15, 20, 22, 27}', '{1, 2, 3, 4, 5, 6}'],
        ];
    }
}
