<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class FisherTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerFISHER
     *
     * @param mixed $expectedResult
     */
    public function testFISHER($expectedResult, ...$args)
    {
        $result = Statistical::FISHER(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerFISHER()
    {
        return require 'data/Calculation/Statistical/FISHER.php';
    }
}
