<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class LookupTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerLOOKUP
     *
     * @param mixed $expectedResult
     */
    public function testLOOKUP($expectedResult, ...$args)
    {
        $result = LookupRef::LOOKUP(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerLOOKUP()
    {
        return require 'data/Calculation/LookupRef/LOOKUP.php';
    }
}
