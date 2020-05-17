<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerPRODUCT
     *
     * @param mixed $expectedResult
     */
    public function testPRODUCT($expectedResult, ...$args)
    {
        $result = MathTrig::PRODUCT(...$args);
        $this->assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerPRODUCT()
    {
        return require 'tests/data/Calculation/MathTrig/PRODUCT.php';
    }
}
