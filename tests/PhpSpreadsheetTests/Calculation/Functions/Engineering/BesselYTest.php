<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class BesselYTest extends TestCase
{
    const BESSEL_PRECISION = 1E-8;

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

    public function providerBESSELY()
    {
        return require 'tests/data/Calculation/Engineering/BESSELY.php';
    }
}
