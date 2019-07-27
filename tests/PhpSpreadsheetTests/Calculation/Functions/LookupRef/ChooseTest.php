<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class ChooseTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCHOOSE
     *
     * @param mixed $expectedResult
     */
    public function testCHOOSE($expectedResult, ...$args)
    {
        $result = LookupRef::CHOOSE(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCHOOSE()
    {
        return require 'data/Calculation/LookupRef/CHOOSE.php';
    }
}
