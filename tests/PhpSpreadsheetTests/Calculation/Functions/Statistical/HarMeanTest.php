<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class HarMeanTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerHARMEAN
     *
     * @param mixed $expectedResult
     */
    public function testHARMEAN($expectedResult, ...$args)
    {
        $result = Statistical::HARMEAN(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerHARMEAN()
    {
        return require 'data/Calculation/Statistical/HARMEAN.php';
    }
}
