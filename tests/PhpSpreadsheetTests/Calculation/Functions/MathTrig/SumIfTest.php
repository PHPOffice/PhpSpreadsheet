<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class SumIfTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerSUMIF
     *
     * @param mixed $expectedResult
     */
    public function testSUMIF($expectedResult, ...$args)
    {
        $result = MathTrig::SUMIF(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerSUMIF()
    {
        return require 'data/Calculation/MathTrig/SUMIF.php';
    }
}
