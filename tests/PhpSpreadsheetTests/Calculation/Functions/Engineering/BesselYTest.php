<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class BesselYTest extends TestCase
{
    const BESSEL_PRECISION = 1E-12;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBESSELY
     *
     * @param mixed $expectedResult
     */
    public function testBESSELY($expectedResult, ...$args): void
    {
        $result = Engineering::BESSELY(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, self::BESSEL_PRECISION);
    }

    public function providerBESSELY(): array
    {
        return require 'tests/data/Calculation/Engineering/BESSELY.php';
    }

    /**
     * @dataProvider providerBesselYArray
     */
    public function testBesselYArray(array $expectedResult, string $value, string $ord): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BESSELY({$value}, {$ord})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerBesselYArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [-3.005455650891885, -0.9315730314941618, -0.44451873376270784, 0.25821685699105446, 0.4980703584466886],
                    [-63.67859624529592, -2.7041052277866418, -1.4714723918672943, -0.5843640364184131, 0.14591813750831284],
                    [-12732.713793408293, -20.701268790798974, -5.441370833706469, -1.1931993152605154, -0.3813358484400383],
                ],
                '{0.01, 0.25, 0.5, 1.25, 2.5}',
                '{0; 1; 2}',
            ],
        ];
    }
}
