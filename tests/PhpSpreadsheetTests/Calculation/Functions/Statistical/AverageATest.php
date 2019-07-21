<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class AverageATest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerAVERAGEA
     *
     * @param mixed $expectedResult
     */
    public function testAVERAGEA($expectedResult, ...$args)
    {
        $result = Statistical::AVERAGEA(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerAVERAGEA()
    {
        return require 'data/Calculation/Statistical/AVERAGEA.php';
    }
}
