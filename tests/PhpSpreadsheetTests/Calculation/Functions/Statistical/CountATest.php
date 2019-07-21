<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class CountATest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCOUNTA
     *
     * @param mixed $expectedResult
     */
    public function testCOUNTA($expectedResult, ...$args)
    {
        $result = Statistical::COUNTA(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerCOUNTA()
    {
        return require 'data/Calculation/Statistical/COUNTA.php';
    }
}
