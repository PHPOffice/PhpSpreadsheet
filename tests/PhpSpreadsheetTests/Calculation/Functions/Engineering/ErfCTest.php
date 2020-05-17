<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ErfCTest extends TestCase
{
    const ERF_PRECISION = 1E-12;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerERFC
     *
     * @param mixed $expectedResult
     */
    public function testERFC($expectedResult, ...$args)
    {
        $result = Engineering::ERFC(...$args);
        $this->assertEquals($expectedResult, $result);
        $this->assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);
    }

    public function providerERFC()
    {
        return require 'tests/data/Calculation/Engineering/ERFC.php';
    }
}
