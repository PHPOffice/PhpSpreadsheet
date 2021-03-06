<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ImaginaryTest extends TestCase
{
    const COMPLEX_PRECISION = 1E-8;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIMAGINARY
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMAGINARY($expectedResult, $value): void
    {
        $result = Engineering::IMAGINARY($value);
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    public function providerIMAGINARY(): array
    {
        return require 'tests/data/Calculation/Engineering/IMAGINARY.php';
    }
}
