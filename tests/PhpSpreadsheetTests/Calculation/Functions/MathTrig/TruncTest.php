<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class TruncTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerTRUNC
     *
     * @param mixed $expectedResult
     */
    public function testTRUNC($expectedResult, ...$args)
    {
        $result = MathTrig::TRUNC(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerTRUNC()
    {
        return require 'data/Calculation/MathTrig/TRUNC.php';
    }
}
