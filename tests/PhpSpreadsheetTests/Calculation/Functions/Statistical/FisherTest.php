<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class FisherTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerFISHER
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testFISHER($expectedResult, $value)
    {
        $result = Statistical::FISHER($value);
        $this->assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerFISHER()
    {
        return require 'data/Calculation/Statistical/FISHER.php';
    }
}
