<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class BesselJTest extends TestCase
{
    const BESSEL_PRECISION = 1E-8;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBESSEJ
     *
     * @param mixed $expectedResult
     */
    public function testBESSELJ($expectedResult, ...$args): void
    {
        $result = Engineering::BESSELJ(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, self::BESSEL_PRECISION);
    }

    public function providerBESSEJ(): array
    {
        return require 'tests/data/Calculation/Engineering/BESSELJ.php';
    }

    /**
     * @dataProvider providerBesselJArray
     */
    public function testBesselJArray(array $expectedResult, string $value, string $ord): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BESSELJ({$value}, {$ord})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerBesselJArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [0.6711327417644983, 0.9384698074235406, 1.00000000283141, 0.9844359313618615, -0.04838377582675685],
                    [-0.4982890574931824, -0.24226845767957006, 0.0, 0.12402597733693042, 0.49709410250442176],
                    [0.15934901834766313, 0.03060402345868265, 0.0, 0.007771889285962677, 0.44605905783029426],
                ],
                '{-1.2, -0.5, 0.0, 0.25, 2.5}',
                '{0; 1; 2}',
            ],
        ];
    }
}
