<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ComplexTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCOMPLEX
     *
     * @param mixed $expectedResult
     */
    public function testCOMPLEX($expectedResult, ...$args): void
    {
        $result = Engineering::COMPLEX(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCOMPLEX(): array
    {
        return require 'tests/data/Calculation/Engineering/COMPLEX.php';
    }
}
