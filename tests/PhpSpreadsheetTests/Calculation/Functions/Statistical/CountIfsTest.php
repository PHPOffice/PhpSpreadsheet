<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class CountIfsTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCOUNTIFS
     *
     * @param mixed $expectedResult
     */
    public function testCOUNTIFS($expectedResult, ...$args)
    {
        $result = Statistical::COUNTIFS(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerCOUNTIFS()
    {
        return require 'data/Calculation/Statistical/COUNTIFS.php';
    }
}
