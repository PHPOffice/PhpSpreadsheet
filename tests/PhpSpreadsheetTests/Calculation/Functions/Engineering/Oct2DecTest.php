<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class Oct2DecTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerOCT2DEC
     *
     * @param mixed $expectedResult
     */
    public function testOCT2DEC($expectedResult, ...$args)
    {
        $result = Engineering::OCTTODEC(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerOCT2DEC()
    {
        return require 'data/Calculation/Engineering/OCT2DEC.php';
    }
}
