<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class Hex2BinTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerHEX2BIN
     *
     * @param mixed $expectedResult
     */
    public function testHEX2BIN($expectedResult, ...$args): void
    {
        $result = Engineering::HEXTOBIN(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerHEX2BIN()
    {
        return require 'tests/data/Calculation/Engineering/HEX2BIN.php';
    }
}
