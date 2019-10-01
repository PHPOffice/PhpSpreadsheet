<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerLOG
     *
     * @param mixed $expectedResult
     */
    public function testLOG($expectedResult, ...$args)
    {
        $result = MathTrig::logBase(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerLOG()
    {
        return require 'data/Calculation/MathTrig/LOG.php';
    }
}
