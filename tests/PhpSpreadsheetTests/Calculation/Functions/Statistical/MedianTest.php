<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class MedianTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerMEDIAN
     *
     * @param mixed $expectedResult
     */
    public function testMEDIAN($expectedResult, ...$args)
    {
        $result = Statistical::MEDIAN(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerMEDIAN()
    {
        return require 'data/Calculation/Statistical/MEDIAN.php';
    }
}
