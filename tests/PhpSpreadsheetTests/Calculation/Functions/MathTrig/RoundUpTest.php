<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class RoundUpTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerROUNDUP
     *
     * @param mixed $expectedResult
     */
    public function testROUNDUP($expectedResult, ...$args)
    {
        $result = MathTrig::ROUNDUP(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerROUNDUP()
    {
        return require 'data/Calculation/MathTrig/ROUNDUP.php';
    }
}
