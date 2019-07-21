<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class ChiDistTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCHIDIST
     *
     * @param mixed $expectedResult
     */
    public function testCHIDIST($expectedResult, ...$args)
    {
        $result = Statistical::CHIDIST(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerCHIDIST()
    {
        return require 'data/Calculation/Statistical/CHIDIST.php';
    }
}
