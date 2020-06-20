<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class BitRShiftTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBITRSHIFT
     *
     * @param mixed $expectedResult
     * @param mixed[] $args
     */
    public function testBITRSHIFT($expectedResult, array $args): void
    {
        $result = Engineering::BITRSHIFT(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerBITRSHIFT()
    {
        return require 'tests/data/Calculation/Engineering/BITRSHIFT.php';
    }
}
