<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class BinomDistTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBINOMDIST
     *
     * @param mixed $expectedResult
     */
    public function testBINOMDIST($expectedResult, ...$args)
    {
        $result = Statistical::BINOMDIST(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerBINOMDIST()
    {
        return require 'data/Calculation/Statistical/BINOMDIST.php';
    }
}
