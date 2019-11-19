<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class ExponDistTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerEXPONDIST
     *
     * @param mixed $expectedResult
     */
    public function testEXPONDIST($expectedResult, ...$args)
    {
        $result = Statistical::EXPONDIST(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerEXPONDIST()
    {
        return require 'data/Calculation/Statistical/EXPONDIST.php';
    }
}
