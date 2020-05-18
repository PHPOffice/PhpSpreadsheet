<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class Bin2HexTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBIN2HEX
     *
     * @param mixed $expectedResult
     */
    public function testBIN2HEX($expectedResult, ...$args): void
    {
        $result = Engineering::BINTOHEX(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerBIN2HEX()
    {
        return require 'tests/data/Calculation/Engineering/BIN2HEX.php';
    }
}
