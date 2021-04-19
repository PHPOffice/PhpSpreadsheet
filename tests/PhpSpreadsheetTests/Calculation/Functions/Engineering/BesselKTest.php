<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class BesselKTest extends TestCase
{
    const BESSEL_PRECISION = 1E-12;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBESSELK
     *
     * @param mixed $expectedResult
     */
    public function testBESSELK($expectedResult, ...$args): void
    {
        $result = Engineering::BESSELK(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, self::BESSEL_PRECISION);
    }

    public function providerBESSELK(): array
    {
        return require 'tests/data/Calculation/Engineering/BESSELK.php';
    }
}
