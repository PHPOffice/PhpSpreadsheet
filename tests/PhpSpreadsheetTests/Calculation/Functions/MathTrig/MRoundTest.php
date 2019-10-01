<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class MRoundTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerMROUND
     *
     * @param mixed $expectedResult
     */
    public function testMROUND($expectedResult, ...$args)
    {
        $result = MathTrig::MROUND(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerMROUND()
    {
        return require 'data/Calculation/MathTrig/MROUND.php';
    }
}
