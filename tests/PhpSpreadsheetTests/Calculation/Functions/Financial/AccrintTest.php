<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class AccrintTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerACCRINT
     *
     * @param mixed $expectedResult
     */
    public function testACCRINT($expectedResult, ...$args)
    {
        $result = Financial::ACCRINT(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-8);
    }

    public function providerACCRINT()
    {
        return require 'data/Calculation/Financial/ACCRINT.php';
    }
}
