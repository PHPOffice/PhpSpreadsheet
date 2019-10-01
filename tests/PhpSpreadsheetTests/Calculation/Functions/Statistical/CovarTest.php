<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class CovarTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCOVAR
     *
     * @param mixed $expectedResult
     */
    public function testCOVAR($expectedResult, ...$args)
    {
        $result = Statistical::COVAR(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerCOVAR()
    {
        return require 'data/Calculation/Statistical/COVAR.php';
    }
}
