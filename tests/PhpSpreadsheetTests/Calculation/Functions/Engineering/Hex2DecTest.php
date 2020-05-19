<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class Hex2DecTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerHEX2DEC
     *
     * @param mixed $expectedResult
     */
    public function testHEX2DEC($expectedResult, ...$args): void
    {
        $result = Engineering::HEXTODEC(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerHEX2DEC()
    {
        return require 'tests/data/Calculation/Engineering/HEX2DEC.php';
    }
}
