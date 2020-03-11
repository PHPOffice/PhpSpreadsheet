<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBASE
     *
     * @param mixed $expectedResult
     */
    public function testBASE($expectedResult, ...$args)
    {
        $result = MathTrig::BASE(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerBASE()
    {
        return require 'data/Calculation/MathTrig/BASE.php';
    }
}
