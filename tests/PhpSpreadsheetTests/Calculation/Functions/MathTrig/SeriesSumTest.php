<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class SeriesSumTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerSERIESSUM
     *
     * @param mixed $expectedResult
     */
    public function testSERIESSUM($expectedResult, ...$args)
    {
        $result = MathTrig::SERIESSUM(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerSERIESSUM()
    {
        return require 'data/Calculation/MathTrig/SERIESSUM.php';
    }
}
