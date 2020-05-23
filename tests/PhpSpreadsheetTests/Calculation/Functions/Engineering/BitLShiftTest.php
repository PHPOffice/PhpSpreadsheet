<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class BitLShiftTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBITLSHIFT
     *
     * @param mixed $expectedResult
     * @param mixed[] $args
     */
    public function testBITLSHIFT($expectedResult, array $args): void
    {
        $result = Engineering::BITLSHIFT(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerBITLSHIFT()
    {
        return require 'tests/data/Calculation/Engineering/BITLSHIFT.php';
    }
}
