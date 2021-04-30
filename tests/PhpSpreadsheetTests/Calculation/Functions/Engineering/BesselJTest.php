<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

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
}
