<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ParseComplexTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    public function testParseComplex()
    {
        [$real, $imaginary, $suffix] = [1.23e-4, 5.67e+8, 'j'];

        $result = Engineering::parseComplex('1.23e-4+5.67e+8j');
        $this->assertArrayHasKey('real', $result);
        $this->assertEquals($real, $result['real']);
        $this->assertArrayHasKey('imaginary', $result);
        $this->assertEquals($imaginary, $result['imaginary']);
        $this->assertArrayHasKey('suffix', $result);
        $this->assertEquals($suffix, $result['suffix']);
    }
}
