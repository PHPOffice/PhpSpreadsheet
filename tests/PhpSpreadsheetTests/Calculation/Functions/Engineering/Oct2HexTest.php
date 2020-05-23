<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class Oct2HexTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerOCT2HEX
     *
     * @param mixed $expectedResult
     */
    public function testOCT2HEX($expectedResult, ...$args): void
    {
        $result = Engineering::OCTTOHEX(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerOCT2HEX()
    {
        return require 'tests/data/Calculation/Engineering/OCT2HEX.php';
    }
}
