<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ImAbsTest extends TestCase
{
    const COMPLEX_PRECISION = 1E-8;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIMABS
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMABS($expectedResult, $value): void
    {
        $result = Engineering::IMABS($value);
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    public function providerIMABS(): array
    {
        return require 'tests/data/Calculation/Engineering/IMABS.php';
    }
}
