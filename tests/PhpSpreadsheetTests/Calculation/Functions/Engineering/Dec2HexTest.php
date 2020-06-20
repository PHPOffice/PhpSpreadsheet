<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class Dec2HexTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerDEC2HEX
     *
     * @param mixed $expectedResult
     */
    public function testDEC2HEX($expectedResult, ...$args): void
    {
        $result = Engineering::DECTOHEX(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerDEC2HEX()
    {
        return require 'tests/data/Calculation/Engineering/DEC2HEX.php';
    }
}
