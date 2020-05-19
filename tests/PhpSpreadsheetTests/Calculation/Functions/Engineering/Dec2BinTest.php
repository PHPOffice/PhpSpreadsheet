<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class Dec2BinTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerDEC2BIN
     *
     * @param mixed $expectedResult
     */
    public function testDEC2BIN($expectedResult, ...$args): void
    {
        $result = Engineering::DECTOBIN(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerDEC2BIN()
    {
        return require 'tests/data/Calculation/Engineering/DEC2BIN.php';
    }
}
