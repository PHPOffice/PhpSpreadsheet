<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class Oct2BinTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerOCT2BIN
     *
     * @param mixed $expectedResult
     */
    public function testOCT2BIN($expectedResult, ...$args)
    {
        $result = Engineering::OCTTOBIN(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerOCT2BIN()
    {
        return require 'data/Calculation/Engineering/OCT2BIN.php';
    }
}
