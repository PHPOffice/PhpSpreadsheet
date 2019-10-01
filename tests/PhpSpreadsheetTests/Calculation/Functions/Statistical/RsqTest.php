<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class RsqTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerRSQ
     *
     * @param mixed $expectedResult
     */
    public function testRSQ($expectedResult, array $xargs, array $yargs)
    {
        $result = Statistical::RSQ($xargs, $yargs);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerRSQ()
    {
        return require 'data/Calculation/Statistical/RSQ.php';
    }
}
