<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ImArgumentTest extends TestCase
{
    const COMPLEX_PRECISION = 1E-8;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIMARGUMENT
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMARGUMENT($expectedResult, $value)
    {
        $result = Engineering::IMARGUMENT($value);
        $this->assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    public function providerIMARGUMENT()
    {
        return require 'data/Calculation/Engineering/IMARGUMENT.php';
    }
}
